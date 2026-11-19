<?php

namespace App\Services;

class VnPayService
{
    protected string $tmnCode;
    protected string $hashSecret;
    protected string $vnpUrl;
    protected string $returnUrl;

    public function __construct()
    {
        $this->tmnCode    = trim((string) config('services.vnpay.tmn_code'));
        $this->hashSecret = trim((string) config('services.vnpay.hash_secret'));
        $this->vnpUrl     = trim((string) config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'));
        $this->returnUrl  = trim((string) config('services.vnpay.return_url', 'http://localhost:5173/payment/vnpay-return'));
    }

    /**
     * Kiểm tra cấu hình TmnCode & HashSecret
     */
    public function isConfigured(): array
    {
        if ($this->tmnCode === '' || $this->hashSecret === '') {
            return [false, 'Thiếu cấu hình VNPAY_TMN_CODE hoặc VNPAY_HASH_SECRET trong file .env.'];
        }

        return [true, 'OK'];
    }

    /**
     * Tạo URL thanh toán VNPAY 2.1.0 chuẩn chữ ký HMAC-SHA512
     */
    public function createPaymentUrl(array $params): array
    {
        [$ok, $msg] = $this->isConfigured();
        if (!$ok) {
            return [
                'success' => false,
                'message' => $msg,
            ];
        }

        $amountVnd = (int) round((float) ($params['amount'] ?? 0));
        if ($amountVnd < 5000) {
            return [
                'success' => false,
                'message' => 'Cổng VNPAY quy định số tiền thanh toán tối thiểu là 5.000đ (hiện tại đơn của bạn là ' . number_format($amountVnd, 0, ',', '.') . 'đ). Vui lòng tăng số lượng hoặc chọn PayOS VietQR!',
            ];
        }

        $txnRef    = (string) $params['txnRef'];
        $orderInfo = (string) ($params['orderInfo'] ?? ('Thanh toan don hang ' . $txnRef));
        $ipAddr    = (string) ($params['ipAddr'] ?? '127.0.0.1');
        if ($ipAddr === '::1' || $ipAddr === '') {
            $ipAddr = '127.0.0.1';
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $createDate = date('YmdHis');
        $expireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $this->tmnCode,
            'vnp_Amount'     => $amountVnd * 100,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => $createDate,
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $ipAddr,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => $orderInfo,
            'vnp_OrderType'  => 'other',
            'vnp_ReturnUrl'  => $this->returnUrl,
            'vnp_TxnRef'     => $txnRef,
            'vnp_ExpireDate' => $expireDate,
        ];

        if (!empty($params['bankCode'])) {
            $inputData['vnp_BankCode'] = (string) $params['bankCode'];
        }

        ksort($inputData);

        $query    = '';
        $hashdata = '';
        $i        = 0;

        foreach ($inputData as $key => $value) {
            $encKey = urlencode((string) $key);
            $encVal = urlencode((string) $value);
            if ($i === 1) {
                $hashdata .= '&' . $encKey . '=' . $encVal;
            } else {
                $hashdata .= $encKey . '=' . $encVal;
                $i         = 1;
            }
            $query .= $encKey . '=' . $encVal . '&';
        }

        $vnpSecureHash = hash_hmac('sha512', $hashdata, $this->hashSecret);
        $paymentUrl    = $this->vnpUrl . '?' . $query . 'vnp_SecureHash=' . $vnpSecureHash;

        return [
            'success'     => true,
            'payment_url' => $paymentUrl,
            'txn_ref'     => $txnRef,
            'amount'      => $amountVnd,
        ];
    }

    /**
     * Xác thực chữ ký phản hồi HMAC-SHA512 từ VNPAY (Return URL & IPN Webhook)
     */
    public function verifySignature(array $queryParams): bool
    {
        if ($this->hashSecret === '') {
            return false;
        }

        $secureHash = (string) ($queryParams['vnp_SecureHash'] ?? '');
        if ($secureHash === '') {
            return false;
        }

        $inputData = [];
        foreach ($queryParams as $key => $value) {
            if (str_starts_with((string) $key, 'vnp_') && !in_array($key, ['vnp_SecureHash', 'vnp_SecureHashType'], true)) {
                $inputData[$key] = $value;
            }
        }

        ksort($inputData);

        $hashData = '';
        $i        = 0;
        foreach ($inputData as $key => $value) {
            $encKey = urlencode((string) $key);
            $encVal = urlencode((string) $value);
            if ($i === 1) {
                $hashData .= '&' . $encKey . '=' . $encVal;
            } else {
                $hashData .= $encKey . '=' . $encVal;
                $i         = 1;
            }
        }

        $calculatedHash = hash_hmac('sha512', $hashData, $this->hashSecret);

        return hash_equals(strtolower($calculatedHash), strtolower($secureHash));
    }

    /**
     * Giải thích mã phản hồi VNPAY thân thiện bằng tiếng Việt
     */
    public function getResponseMessage(string $code): string
    {
        $map = [
            '00' => 'Giao dịch thanh toán thành công!',
            '07' => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (liên quan tới lừa đảo, giao dịch bất thường).',
            '09' => 'Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.',
            '10' => 'Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần.',
            '11' => 'Đã hết hạn chờ thanh toán. Vui lòng thực hiện lại giao dịch.',
            '12' => 'Thẻ/Tài khoản của khách hàng bị khóa.',
            '13' => 'Quý khách nhập sai mật khẩu xác thực giao dịch (OTP).',
            '24' => 'Khách hàng đã hủy giao dịch thanh toán.',
            '51' => 'Tài khoản của quý khách không đủ số dư để thực hiện giao dịch.',
            '65' => 'Tài khoản của quý khách đã vượt quá hạn mức giao dịch trong ngày.',
            '75' => 'Ngân hàng thanh toán đang bảo trì.',
            '79' => 'Khách hàng nhập sai mật khẩu thanh toán quá số lần quy định.',
            '99' => 'Lỗi khác từ cổng thanh toán VNPAY.',
        ];

        return $map[$code] ?? ('Giao dịch không thành công (Mã lỗi VNPAY: ' . $code . ').');
    }
}
