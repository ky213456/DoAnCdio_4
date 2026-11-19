<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayOsService
{
    protected string $clientId;
    protected string $apiKey;
    protected string $checksumKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->clientId    = trim((string) config('services.payos.client_id'));
        $this->apiKey      = trim((string) config('services.payos.api_key'));
        $this->checksumKey = trim((string) config('services.payos.checksum_key'));
        $this->baseUrl     = rtrim((string) config('services.payos.base_url', 'https://api-merchant.payos.vn'), '/');
    }

    /**
     * Kiểm tra bộ 3 Key PayOS đã được cấu hình đầy đủ chưa (Checksum Key chuẩn 64 ký tự hex)
     */
    public function isConfigured(): array
    {
        if ($this->clientId === '' || $this->apiKey === '' || $this->checksumKey === '') {
            return [false, 'Thiếu cấu hình PAYOS_CLIENT_ID, PAYOS_API_KEY hoặc PAYOS_CHECKSUM_KEY trong .env.'];
        }

        if (str_contains($this->checksumKey, 'THAY_BANG') || strlen($this->checksumKey) < 60) {
            return [false, 'PAYOS_CHECKSUM_KEY trong file .env chưa đủ 64 ký tự (do bị cắt trong ảnh chụp). Vui lòng bấm nút Copy Checksum Key trên my.payos.vn và dán đủ vào .env.'];
        }

        return [true, 'OK'];
    }

    /**
     * Tạo chữ ký HMAC-SHA256 chuẩn tài liệu PayOS cho yêu cầu tạo link thanh toán
     */
    public function generateCreateLinkSignature(int $amount, string $cancelUrl, string $description, int $orderCode, string $returnUrl): string
    {
        $dataStr = sprintf(
            'amount=%d&cancelUrl=%s&description=%s&orderCode=%d&returnUrl=%s',
            $amount,
            $cancelUrl,
            $description,
            $orderCode,
            $returnUrl
        );

        return hash_hmac('sha256', $dataStr, $this->checksumKey);
    }

    /**
     * Tạo liên kết thanh toán VietQR qua PayOS API v2
     */
    public function createPaymentLink(array $params): array
    {
        [$ok, $msg] = $this->isConfigured();
        if (!$ok) {
            return [
                'success' => false,
                'message' => $msg,
            ];
        }

        $orderCode   = (int) $params['orderCode'];
        $amount      = max(1000, (int) $params['amount']);
        $description = mb_substr((string) ($params['description'] ?? ('DH' . $orderCode)), 0, 25);
        $returnUrl   = (string) $params['returnUrl'];
        $cancelUrl   = (string) $params['cancelUrl'];
        $items       = $params['items'] ?? [];

        $signature = $this->generateCreateLinkSignature(
            $amount,
            $cancelUrl,
            $description,
            $orderCode,
            $returnUrl
        );

        $payload = [
            'orderCode'   => $orderCode,
            'amount'      => $amount,
            'description' => $description,
            'cancelUrl'   => $cancelUrl,
            'returnUrl'   => $returnUrl,
            'signature'   => $signature,
        ];

        if (!empty($items)) {
            $payload['items'] = $items;
        }
        if (!empty($params['buyerName'])) {
            $payload['buyerName'] = (string) $params['buyerName'];
        }
        if (!empty($params['buyerPhone'])) {
            $payload['buyerPhone'] = (string) $params['buyerPhone'];
        }
        if (!empty($params['buyerAddress'])) {
            $payload['buyerAddress'] = (string) $params['buyerAddress'];
        }
        if (!empty($params['expiredAt'])) {
            $payload['expiredAt'] = (int) $params['expiredAt'];
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(20)
                ->withHeaders([
                    'x-client-id'  => $this->clientId,
                    'x-api-key'    => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/v2/payment-requests", $payload);

            $json = $response->json();

            if ($response->successful() && ($json['code'] ?? '') === '00' && !empty($json['data']['checkoutUrl'])) {
                return [
                    'success' => true,
                    'data'    => $json['data'],
                ];
            }

            Log::error('PayOS createPaymentLink error', [
                'status' => $response->status(),
                'body'   => $json ?? $response->body(),
            ]);

            return [
                'success' => false,
                'code'    => $json['code'] ?? (string) $response->status(),
                'message' => $json['desc'] ?? 'Không tạo được liên kết thanh toán PayOS.',
            ];
        } catch (\Throwable $e) {
            Log::error('PayOS createPaymentLink exception: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Lỗi kết nối tới máy chủ PayOS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Truy vấn trực tiếp trạng thái giao dịch từ máy chủ PayOS (dùng để verify trên localhost & đối soát Admin)
     */
    public function getPaymentInfo(int|string $orderCode): array
    {
        if ($this->clientId === '' || $this->apiKey === '') {
            return [
                'success' => false,
                'message' => 'Thiếu PAYOS_CLIENT_ID hoặc PAYOS_API_KEY.',
            ];
        }

        try {
            $response = Http::withoutVerifying()
                ->timeout(20)
                ->withHeaders([
                    'x-client-id' => $this->clientId,
                    'x-api-key'   => $this->apiKey,
                ])
                ->get("{$this->baseUrl}/v2/payment-requests/{$orderCode}");

            $json = $response->json();

            if ($response->successful() && ($json['code'] ?? '') === '00' && !empty($json['data'])) {
                return [
                    'success' => true,
                    'data'    => $json['data'],
                ];
            }

            return [
                'success' => false,
                'code'    => $json['code'] ?? (string) $response->status(),
                'message' => $json['desc'] ?? 'Không tìm thấy giao dịch trên PayOS.',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Lỗi truy vấn PayOS: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Xác thực chữ ký Webhook từ PayOS (HMAC-SHA256 theo thứ tự key alphabet)
     */
    public function verifyWebhookSignature(array $webhookBody): bool
    {
        $signature = $webhookBody['signature'] ?? '';
        $data      = $webhookBody['data'] ?? null;

        if (!is_array($data) || $signature === '' || $this->checksumKey === '') {
            return false;
        }

        $sorted = $this->sortDataByKey($data);
        $parts  = [];
        foreach ($sorted as $key => $value) {
            if (is_array($value)) {
                $value = json_encode($this->sortRecursive($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } elseif (in_array($value, [null, 'null', 'undefined'], true)) {
                $value = '';
            }
            $parts[] = "{$key}={$value}";
        }

        $queryStr = implode('&', $parts);
        $expected = hash_hmac('sha256', $queryStr, $this->checksumKey);

        return hash_equals($expected, $signature);
    }

    protected function sortDataByKey(array $data): array
    {
        ksort($data);
        return $data;
    }

    protected function sortRecursive(array $arr): array
    {
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = $this->sortRecursive($v);
            }
        }
        ksort($arr);
        return $arr;
    }
}
