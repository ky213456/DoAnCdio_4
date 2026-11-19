<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\ThanhToan;
use App\Services\PayOsService;
use App\Services\VnPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ThanhToanController extends Controller
{
    protected PayOsService $payOsService;
    protected VnPayService $vnPayService;

    public function __construct(PayOsService $payOsService, VnPayService $vnPayService)
    {
        $this->payOsService = $payOsService;
        $this->vnPayService = $vnPayService;
    }

    /**
     * GET /api/payment/payos/verify/{orderCode}
     * Xác thực trực tiếp trạng thái giao dịch với máy chủ PayOS (chạy mượt trên localhost không cần Webhook công khai)
     */
    public function verifyPayOs(string $orderCode)
    {
        $maGiaoDich = 'PAYOS_' . $orderCode;
        $payment    = ThanhToan::where('ma_giao_dich', $maGiaoDich)->first();

        if (!$payment) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy giao dịch #' . $orderCode . ' trong hệ thống TechMart.',
            ], 404);
        }

        $order = DonHang::find($payment->don_hang_id);

        // Truy vấn trạng thái mới nhất từ PayOS API v2
        $payOsRes = $this->payOsService->getPaymentInfo($orderCode);

        if (!$payOsRes['success']) {
            return response()->json([
                'status'  => false,
                'message' => $payOsRes['message'] ?? 'Không thể xác thực trạng thái với PayOS.',
                'data'    => [
                    'payment' => $payment,
                    'order'   => $order,
                ],
            ], 400);
        }

        $payOsData   = $payOsRes['data'];
        $payOsStatus = strtoupper((string) ($payOsData['status'] ?? 'PENDING'));

        DB::transaction(function () use ($payment, $order, $payOsData, $payOsStatus) {
            $existingMeta = is_array($payment->du_lieu_phan_hoi) ? $payment->du_lieu_phan_hoi : [];
            $mergedMeta   = array_merge($existingMeta, ['payos_info' => $payOsData]);

            if ($payOsStatus === 'PAID') {
                $payment->trang_thai           = 'success';
                $payment->ma_phan_hoi_cong     = 'PAID';
                $payment->du_lieu_phan_hoi     = $mergedMeta;
                $payment->thoi_gian_thanh_toan = $payment->thoi_gian_thanh_toan ?? now();
                $payment->save();

                if ($order && $order->trang_thai_thanh_toan !== 'da_thanh_toan') {
                    $order->trang_thai_thanh_toan = 'da_thanh_toan';
                    if ($order->trang_thai_don_hang === 'cho_xac_nhan') {
                        $order->trang_thai_don_hang = 'da_xac_nhan';
                    }
                    $order->save();
                }
            } elseif (in_array($payOsStatus, ['CANCELLED', 'EXPIRED'], true)) {
                $payment->trang_thai       = 'failed';
                $payment->ma_phan_hoi_cong = $payOsStatus;
                $payment->du_lieu_phan_hoi = $mergedMeta;
                $payment->save();

                if ($order && $order->trang_thai_thanh_toan === 'chua_thanh_toan') {
                    $order->trang_thai_thanh_toan = 'that_bai';
                    $order->save();
                }
            } else {
                $payment->ma_phan_hoi_cong = $payOsStatus;
                $payment->du_lieu_phan_hoi = $mergedMeta;
                $payment->save();
            }
        });

        $payment->refresh();
        if ($order) {
            $order->refresh();
        }

        return response()->json([
            'status'       => true,
            'payos_status' => $payOsStatus,
            'message'      => $payOsStatus === 'PAID'
                ? 'Thanh toán PayOS thành công! Đơn hàng đã được xác nhận.'
                : ($payOsStatus === 'CANCELLED' ? 'Giao dịch đã bị hủy.' : 'Giao dịch đang chờ thanh toán.'),
            'data' => [
                'order_code' => $orderCode,
                'payment'    => $payment,
                'order'      => $order,
                'payos_data' => $payOsData,
            ],
        ]);
    }

    /**
     * POST /api/payment/payos/webhook
     * Nhận Webhook tự động từ PayOS khi khách chuyển khoản thành công (Verify HMAC-SHA256)
     */
    public function payOsWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayOS Webhook received', $payload);

        $data      = $payload['data'] ?? [];
        $orderCode = $data['orderCode'] ?? null;

        // Xử lý request test webhook mặc định của PayOS (orderCode = 123)
        if ((int) $orderCode === 123) {
            return response()->json([
                'error'   => 0,
                'message' => 'Webhook ping OK',
                'data'    => $data,
            ]);
        }

        if (!$this->payOsService->verifyWebhookSignature($payload)) {
            Log::warning('PayOS Webhook invalid signature', $payload);

            return response()->json([
                'error'   => -1,
                'message' => 'Invalid signature',
            ], 400);
        }

        $code    = $payload['code'] ?? ($data['code'] ?? '');
        $success = ($payload['success'] ?? false) === true || $code === '00';

        if ($orderCode) {
            $payment = ThanhToan::where('ma_giao_dich', 'PAYOS_' . $orderCode)->first();
            if ($payment) {
                $order = DonHang::find($payment->don_hang_id);

                DB::transaction(function () use ($payment, $order, $payload, $success, $code) {
                    $existingMeta = is_array($payment->du_lieu_phan_hoi) ? $payment->du_lieu_phan_hoi : [];
                    $payment->du_lieu_phan_hoi = array_merge($existingMeta, ['webhook' => $payload]);

                    if ($success) {
                        $payment->trang_thai           = 'success';
                        $payment->ma_phan_hoi_cong     = 'PAID';
                        $payment->thoi_gian_thanh_toan = $payment->thoi_gian_thanh_toan ?? now();
                        $payment->save();

                        if ($order) {
                            $order->trang_thai_thanh_toan = 'da_thanh_toan';
                            if ($order->trang_thai_don_hang === 'cho_xac_nhan') {
                                $order->trang_thai_don_hang = 'da_xac_nhan';
                            }
                            $order->save();
                        }
                    } else {
                        $payment->trang_thai       = 'failed';
                        $payment->ma_phan_hoi_cong = (string) $code;
                        $payment->save();
                    }
                });
            }
        }

        return response()->json([
            'error'   => 0,
            'message' => 'Webhook processed successfully',
        ]);
    }

    /**
     * GET /api/admin/thanh-toan/get-data
     * Lấy danh sách giao dịch thanh toán (PayOS / VNPAY / COD) cho Admin đối soát
     */
    public function getAdminTransactions(Request $request)
    {
        $gateway = $request->query('cong_thanh_toan');
        $status  = $request->query('trang_thai');

        $query = ThanhToan::leftJoin('don_hang', 'thanh_toan.don_hang_id', '=', 'don_hang.id')
            ->leftJoin('nguoi_dung', 'don_hang.nguoi_dung_id', '=', 'nguoi_dung.id')
            ->select(
                'thanh_toan.*',
                'don_hang.ma_don_hang',
                'don_hang.ho_ten_nguoi_nhan',
                'don_hang.so_dien_thoai_nhan',
                'don_hang.tong_thanh_toan as tong_tien_don_hang',
                'don_hang.trang_thai_thanh_toan as don_hang_tt_thanh_toan',
                'don_hang.trang_thai_don_hang',
                'nguoi_dung.ho_ten as ten_khach_hang'
            )
            ->orderBy('thanh_toan.id', 'desc');

        if ($gateway && $gateway !== 'ALL') {
            $query->where('thanh_toan.cong_thanh_toan', $gateway);
        }

        if ($status && $status !== 'ALL') {
            $query->where('thanh_toan.trang_thai', $status);
        }

        $list = $query->get();

        $summary = [
            'tong_giao_dich'     => ThanhToan::count(),
            'thanh_cong'         => ThanhToan::where('trang_thai', 'success')->count(),
            'dang_cho'           => ThanhToan::where('trang_thai', 'pending')->count(),
            'that_bai'           => ThanhToan::where('trang_thai', 'failed')->count(),
            'tong_tien_payos'    => (float) ThanhToan::where('cong_thanh_toan', 'payos')->where('trang_thai', 'success')->sum('so_tien'),
            'tong_tien_cong'     => (float) ThanhToan::where('trang_thai', 'success')->sum('so_tien'),
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách giao dịch thanh toán thành công',
            'summary' => $summary,
            'data'    => $list,
        ]);
    }

    /**
     * POST /api/admin/thanh-toan/doi-soat
     * Admin bấm đối soát thủ công một giao dịch PayOS trực tiếp với API PayOS
     */
    public function reconcilePayOs(Request $request)
    {
        $id      = $request->input('id');
        $payment = ThanhToan::find($id);

        if (!$payment) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy giao dịch #' . $id,
            ], 404);
        }

        if ($payment->cong_thanh_toan !== 'payos') {
            return response()->json([
                'status'  => false,
                'message' => 'Chức năng đối soát tự động chỉ áp dụng cho cổng PayOS.',
            ], 400);
        }

        $orderCode = str_replace('PAYOS_', '', (string) $payment->ma_giao_dich);
        if (!ctype_digit($orderCode)) {
            return response()->json([
                'status'  => false,
                'message' => 'Mã giao dịch "' . $payment->ma_giao_dich . '" là dữ liệu mẫu cũ không có trên máy chủ PayOS.',
            ], 400);
        }

        return $this->verifyPayOs($orderCode);
    }

    /**
     * GET /api/payment/vnpay/verify
     * Xác thực chữ ký HMAC-SHA512 & cập nhật trạng thái đơn hàng khi VNPAY redirect về Return URL
     */
    public function verifyVnPay(Request $request)
    {
        $queryParams = $request->query();
        $vnpTxnRef   = (string) ($queryParams['vnp_TxnRef'] ?? '');

        if ($vnpTxnRef === '') {
            return response()->json([
                'status'  => false,
                'message' => 'Thiếu tham số mã giao dịch vnp_TxnRef từ VNPAY.',
            ], 400);
        }

        if (!$this->vnPayService->verifySignature($queryParams)) {
            return response()->json([
                'status'  => false,
                'message' => 'Chữ ký bảo mật VNPAY (vnp_SecureHash) không hợp lệ! Giao dịch bị từ chối để chống giả mạo.',
            ], 400);
        }

        $payment = ThanhToan::where('ma_giao_dich', $vnpTxnRef)->first();
        if (!$payment) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy giao dịch #' . $vnpTxnRef . ' trong CSDL TechMart.',
            ], 404);
        }

        $order          = DonHang::find($payment->don_hang_id);
        $responseCode   = (string) ($queryParams['vnp_ResponseCode'] ?? '99');
        $txStatus       = (string) ($queryParams['vnp_TransactionStatus'] ?? $responseCode);
        $isSuccess      = $responseCode === '00' && $txStatus === '00';
        $vnpMsg         = $this->vnPayService->getResponseMessage($responseCode);

        DB::transaction(function () use ($payment, $order, $queryParams, $responseCode, $isSuccess) {
            $existingMeta = is_array($payment->du_lieu_phan_hoi) ? $payment->du_lieu_phan_hoi : [];
            $mergedMeta   = array_merge($existingMeta, $queryParams);

            if ($isSuccess) {
                $payment->trang_thai           = 'success';
                $payment->ma_phan_hoi_cong     = '00';
                $payment->du_lieu_phan_hoi     = $mergedMeta;
                $payment->thoi_gian_thanh_toan = $payment->thoi_gian_thanh_toan ?? now();
                $payment->save();

                if ($order && $order->trang_thai_thanh_toan !== 'da_thanh_toan') {
                    $order->trang_thai_thanh_toan = 'da_thanh_toan';
                    if ($order->trang_thai_don_hang === 'cho_xac_nhan') {
                        $order->trang_thai_don_hang = 'da_xac_nhan';
                    }
                    $order->save();
                }
            } else {
                $payment->trang_thai       = 'failed';
                $payment->ma_phan_hoi_cong = $responseCode;
                $payment->du_lieu_phan_hoi = $mergedMeta;
                $payment->save();

                if ($order && $order->trang_thai_thanh_toan === 'chua_thanh_toan') {
                    $order->trang_thai_thanh_toan = 'that_bai';
                    $order->save();
                }
            }
        });

        $payment->refresh();
        if ($order) {
            $order->refresh();
        }

        return response()->json([
            'status'        => true,
            'is_paid'       => $isSuccess,
            'response_code' => $responseCode,
            'message'       => $vnpMsg,
            'data'          => [
                'txn_ref'        => $vnpTxnRef,
                'transaction_no' => $queryParams['vnp_TransactionNo'] ?? null,
                'bank_code'      => $queryParams['vnp_BankCode'] ?? null,
                'card_type'      => $queryParams['vnp_CardType'] ?? null,
                'amount'         => isset($queryParams['vnp_Amount']) ? ((float) $queryParams['vnp_Amount'] / 100) : (float) $payment->so_tien,
                'payment'        => $payment,
                'order'          => $order,
            ],
        ]);
    }

    /**
     * GET /api/payment/vnpay/ipn
     * Endpoint Webhook IPN chuẩn VNPAY (trả về RspCode & Message theo tài liệu VNPAY)
     */
    public function vnpayIpn(Request $request)
    {
        $queryParams = $request->query();
        $vnpTxnRef   = (string) ($queryParams['vnp_TxnRef'] ?? '');

        if (!$this->vnPayService->verifySignature($queryParams)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $payment = ThanhToan::where('ma_giao_dich', $vnpTxnRef)->first();
        if (!$payment) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        $vnpAmount = isset($queryParams['vnp_Amount']) ? ((float) $queryParams['vnp_Amount'] / 100) : 0;
        if (abs($vnpAmount - (float) $payment->so_tien) > 1) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        if ($payment->trang_thai === 'success') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        $responseCode = (string) ($queryParams['vnp_ResponseCode'] ?? '99');
        $isSuccess    = $responseCode === '00';
        $order        = DonHang::find($payment->don_hang_id);

        DB::transaction(function () use ($payment, $order, $queryParams, $responseCode, $isSuccess) {
            $existingMeta = is_array($payment->du_lieu_phan_hoi) ? $payment->du_lieu_phan_hoi : [];
            $payment->du_lieu_phan_hoi = array_merge($existingMeta, ['ipn' => $queryParams]);

            if ($isSuccess) {
                $payment->trang_thai           = 'success';
                $payment->ma_phan_hoi_cong     = '00';
                $payment->thoi_gian_thanh_toan = now();
                $payment->save();

                if ($order) {
                    $order->trang_thai_thanh_toan = 'da_thanh_toan';
                    if ($order->trang_thai_don_hang === 'cho_xac_nhan') {
                        $order->trang_thai_don_hang = 'da_xac_nhan';
                    }
                    $order->save();
                }
            } else {
                $payment->trang_thai       = 'failed';
                $payment->ma_phan_hoi_cong = $responseCode;
                $payment->save();
            }
        });

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }
}
