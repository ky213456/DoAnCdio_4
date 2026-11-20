<?php

namespace App\Http\Controllers;

use App\Models\BienTheSanPham;
use App\Models\ChiTietDonHang;
use App\Models\DonHang;
use App\Models\GioHang;
use App\Models\MaGiamGia;
use App\Models\ThanhToan;
use App\Services\PayOsService;
use App\Services\VnPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonHangController extends Controller
{
    // ==========================================
    // 1. ADMIN - QUẢN LÝ ĐƠN HÀNG
    // ==========================================
    public function getData()
    {
        $data = DonHang::leftJoin('nguoi_dung', 'don_hang.nguoi_dung_id', '=', 'nguoi_dung.id')
            ->leftJoin('ma_giam_gia', 'don_hang.ma_giam_gia_id', '=', 'ma_giam_gia.id')
            ->select(
                'don_hang.*',
                'nguoi_dung.ho_ten as ten_khach_hang',
                'nguoi_dung.email as email_khach_hang',
                'nguoi_dung.so_dien_thoai as sdt_khach_hang',
                'ma_giam_gia.ma_code as ma_voucher',
                'ma_giam_gia.ma_code'
            )
            ->with(['items.variant.product', 'serials'])
            ->orderBy('don_hang.id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu đơn hàng thành công',
            'data'    => $data,
        ]);
    }

    public function changeStatus(Request $request)
    {
        $id = $request->input('id');
        $trang_thai = $request->input('trang_thai_don_hang');
        $trang_thai_thanh_toan = $request->input('trang_thai_thanh_toan');

        $donHang = DonHang::find($id);
        if (!$donHang) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy đơn hàng #' . $id
            ], 404);
        }

        if ($trang_thai) {
            $donHang->trang_thai_don_hang = $trang_thai;
        }
        if ($trang_thai_thanh_toan) {
            $donHang->trang_thai_thanh_toan = $trang_thai_thanh_toan;
        }
        $donHang->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật trạng thái đơn hàng #' . ($donHang->ma_don_hang ?? $donHang->id) . ' thành công',
            'data'    => $donHang
        ]);
    }

    // ==========================================
    // 2. CLIENT - ĐẶT HÀNG & THANH TOÁN (VNPAY / PAYOS / COD)
    // ==========================================
    public function checkout(Request $request, PayOsService $payOsService, VnPayService $vnPayService)
    {
        $hoTen    = trim((string) $request->input('ho_ten_nguoi_nhan'));
        $sdt      = trim((string) $request->input('so_dien_thoai_nhan'));
        $diaChi   = trim((string) $request->input('dia_chi_giao_hang'));
        $ghiChu   = trim((string) $request->input('ghi_chu', ''));
        $phuongThuc = strtolower((string) $request->input('phuong_thuc_thanh_toan', 'payos'));
        $userId   = $request->input('user_id') ?: $request->input('nguoi_dung_id');
        $sessionId = $request->input('session_id');
        $maVoucher = strtoupper(trim((string) $request->input('ma_voucher', '')));
        $rawItems = $request->input('items', []);

        if ($hoTen === '' || $sdt === '' || $diaChi === '') {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng nhập đầy đủ Họ tên, Số điện thoại và Địa chỉ giao hàng.',
            ], 422);
        }

        if (!in_array($phuongThuc, ['cod', 'payos', 'vnpay'], true)) {
            $phuongThuc = 'payos';
        }

        $validUserId = null;
        if ($userId && DB::table('nguoi_dung')->where('id', $userId)->exists()) {
            $validUserId = (int) $userId;
        }

        // Nếu FE không gửi items, lấy trực tiếp từ giỏ hàng DB
        if (empty($rawItems) && ($validUserId || $sessionId)) {
            $cartQuery = GioHang::join('bien_the_san_pham', 'gio_hang.bien_the_id', '=', 'bien_the_san_pham.id')
                ->join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id');
            if ($validUserId) {
                $cartQuery->where('gio_hang.nguoi_dung_id', $validUserId);
            } else {
                $cartQuery->where('gio_hang.session_id', $sessionId);
            }
            $rawItems = $cartQuery->select(
                'gio_hang.id as gio_hang_id',
                'gio_hang.bien_the_id',
                'gio_hang.so_luong as quantity',
                'gio_hang.gia as gia_gio_hang',
                'san_pham.ten_san_pham as name',
                'san_pham.gia_goc as sp_gia_goc',
                'san_pham.gia_khuyen_mai as sp_gia_khuyen_mai',
                'bien_the_san_pham.ten_bien_the',
                'bien_the_san_pham.gia_ban',
                'bien_the_san_pham.gia_khuyen_mai'
            )->get()->toArray();
        }

        if (empty($rawItems)) {
            return response()->json([
                'status'  => false,
                'message' => 'Giỏ hàng của bạn đang trống hoặc chưa chọn sản phẩm để thanh toán.',
            ], 422);
        }

        // Chuẩn hóa danh sách sản phẩm & tính tổng tiền
        $orderItemsPayload = [];
        $tongTienHang = 0;
        $cartIdsToRemove = [];

        foreach ($rawItems as $item) {
            $qty = max(1, (int) ($item['quantity'] ?? $item['so_luong'] ?? 1));
            $bienTheId = $item['bien_the_id'] ?? null;
            $tenSanPham = (string) ($item['name'] ?? $item['ten_san_pham'] ?? 'Sản phẩm TechMart');
            $tenBienThe = (string) ($item['ten_bien_the'] ?? $item['specs'] ?? 'Tiêu chuẩn');
            $donGia = (float) ($item['gia_gio_hang'] ?? $item['gia'] ?? $item['price'] ?? $item['don_gia'] ?? 0);
            $cartRecord = !empty($item['gio_hang_id']) ? GioHang::find((int) $item['gio_hang_id']) : null;

            if ($cartRecord && $cartRecord->gia !== null && (float) $cartRecord->gia > 0) {
                $donGia = (float) $cartRecord->gia;
            }

            if ($bienTheId) {
                $variant = BienTheSanPham::with('product')->find($bienTheId);
                if ($variant) {
                    $tenSanPham = $variant->product ? $variant->product->ten_san_pham : $tenSanPham;
                    $tenBienThe = $variant->ten_bien_the ?: $tenBienThe;
                    if ($donGia <= 0) {
                        if ($variant->product && ($variant->product->gia_khuyen_mai > 0 || $variant->product->gia_goc > 0)) {
                            $donGia = ($variant->product->gia_khuyen_mai && $variant->product->gia_khuyen_mai > 0)
                                ? (float) $variant->product->gia_khuyen_mai
                                : (float) $variant->product->gia_goc;
                        } else {
                            $donGia = ($variant->gia_khuyen_mai && $variant->gia_khuyen_mai > 0)
                                ? (float) $variant->gia_khuyen_mai
                                : (float) $variant->gia_ban;
                        }
                    }
                } else {
                    $bienTheId = null;
                }
            }

            if (!empty($item['gio_hang_id'])) {
                $cartIdsToRemove[] = (int) $item['gio_hang_id'];
            }

            $thanhTien = $donGia * $qty;
            $tongTienHang += $thanhTien;

            $orderItemsPayload[] = [
                'bien_the_id'  => $bienTheId,
                'ten_san_pham' => mb_substr($tenSanPham, 0, 250),
                'ten_bien_the' => mb_substr($tenBienThe, 0, 250),
                'so_luong'     => $qty,
                'don_gia'      => $donGia,
                'thanh_tien'   => $thanhTien,
            ];
        }

        // Xử lý Voucher giảm giá từ CSDL ma_giam_gia
        $maGiamGiaId = null;
        $soTienGiam  = 0;
        if ($maVoucher !== '') {
            $voucher = MaGiamGia::where('ma_code', $maVoucher)
                ->where('trang_thai', 1)
                ->first();

            if ($voucher && ($voucher->so_luong - $voucher->da_su_dung) > 0 && $tongTienHang >= (float) $voucher->don_hang_toi_thieu) {
                $maGiamGiaId = $voucher->id;
                if ($voucher->loai_giam_gia === 'percent') {
                    $soTienGiam = $tongTienHang * ((float) $voucher->gia_tri_giam / 100);
                    if ($voucher->giam_toi_da && $soTienGiam > (float) $voucher->giam_toi_da) {
                        $soTienGiam = (float) $voucher->giam_toi_da;
                    }
                } else {
                    $soTienGiam = (float) $voucher->gia_tri_giam;
                }
            } else {
                // Hỗ trợ voucher demo trên giỏ hàng FE nếu có truyền client_discount
                $clientDiscount = (float) $request->input('so_tien_giam', 0);
                if ($clientDiscount > 0 && $clientDiscount <= $tongTienHang) {
                    $soTienGiam = $clientDiscount;
                }
            }
        }

        $phiVanChuyen = 0;
        $tongThanhToan = max(0, $tongTienHang - $soTienGiam + $phiVanChuyen);

        DB::beginTransaction();
        try {
            $maDonHang = 'DH' . now()->format('Ymd') . '-' . str_pad((string) rand(1000, 9999), 4, '0', STR_PAD_LEFT);

            $donHang = DonHang::create([
                'ma_don_hang'           => $maDonHang,
                'nguoi_dung_id'         => $validUserId,
                'ma_giam_gia_id'        => $maGiamGiaId,
                'ho_ten_nguoi_nhan'     => $hoTen,
                'so_dien_thoai_nhan'    => $sdt,
                'dia_chi_giao_hang'     => $diaChi,
                'ghi_chu'               => $ghiChu,
                'tong_tien_hang'        => $tongTienHang,
                'so_tien_giam'          => $soTienGiam,
                'phi_van_chuyen'        => $phiVanChuyen,
                'tong_thanh_toan'       => $tongThanhToan,
                'phuong_thuc_thanh_toan'=> $phuongThuc,
                'trang_thai_thanh_toan' => 'chua_thanh_toan',
                'trang_thai_don_hang'   => 'cho_xac_nhan',
                'ngay_dat'              => now(),
            ]);

            foreach ($orderItemsPayload as $line) {
                ChiTietDonHang::create(array_merge($line, ['don_hang_id' => $donHang->id]));
            }

            if ($maGiamGiaId) {
                MaGiamGia::where('id', $maGiamGiaId)->increment('da_su_dung');
            }

            // Nếu thanh toán qua PayOS -> Gọi API PayOS tạo link VietQR
            if ($phuongThuc === 'payos') {
                // Sinh mã orderCode số nguyên dương duy nhất (<= 15 chữ số)
                $orderCode = (int) (now()->format('ymdHis') . str_pad((string) ($donHang->id % 100), 2, '0', STR_PAD_LEFT));
                $demoAmount = (int) config('services.payos.demo_amount', 0);
                $chargeAmount = $demoAmount > 0 ? $demoAmount : (int) round($tongThanhToan);
                $chargeAmount = max(1000, $chargeAmount);

                $frontendUrl = rtrim((string) env('FRONTEND_URL', 'http://localhost:5173'), '/');
                $returnUrl   = "{$frontendUrl}/payment/payos-return?status=success";
                $cancelUrl   = "{$frontendUrl}/payment/payos-return?status=cancel";

                $payOsItems = array_map(function ($it) {
                    return [
                        'name'     => mb_substr($it['ten_san_pham'], 0, 50),
                        'quantity' => (int) $it['so_luong'],
                        'price'    => (int) round($it['don_gia']),
                    ];
                }, $orderItemsPayload);

                $payOsRes = $payOsService->createPaymentLink([
                    'orderCode'    => $orderCode,
                    'amount'       => $chargeAmount,
                    'description'  => 'TechMart ' . $maDonHang,
                    'returnUrl'    => $returnUrl,
                    'cancelUrl'    => $cancelUrl,
                    'items'        => $payOsItems,
                    'buyerName'    => $hoTen,
                    'buyerPhone'   => $sdt,
                    'buyerAddress' => $diaChi,
                ]);

                if (!$payOsRes['success']) {
                    DB::rollBack();

                    return response()->json([
                        'status'  => false,
                        'message' => $payOsRes['message'] ?? 'Không thể khởi tạo cổng thanh toán PayOS.',
                    ], 400);
                }

                $linkData = $payOsRes['data'];

                ThanhToan::create([
                    'don_hang_id'      => $donHang->id,
                    'ma_giao_dich'     => 'PAYOS_' . $orderCode,
                    'cong_thanh_toan'  => 'payos',
                    'so_tien'          => $chargeAmount,
                    'loai_tien'        => 'VND',
                    'trang_thai'       => 'pending',
                    'ma_phan_hoi_cong' => 'PENDING',
                    'du_lieu_phan_hoi' => [
                        'orderCode'     => $orderCode,
                        'paymentLinkId' => $linkData['paymentLinkId'] ?? null,
                        'checkoutUrl'   => $linkData['checkoutUrl'] ?? null,
                        'qrCode'        => $linkData['qrCode'] ?? null,
                        'demo_mode'     => $demoAmount > 0,
                    ],
                ]);

                // Xóa các sản phẩm đã đặt khỏi giỏ DB
                $this->clearPurchasedCart($cartIdsToRemove, $validUserId, $sessionId);

                DB::commit();

                return response()->json([
                    'status'         => true,
                    'payment_method' => 'payos',
                    'message'        => 'Khởi tạo đơn hàng & liên kết thanh toán PayOS thành công!',
                    'order_code'     => $orderCode,
                    'checkout_url'   => $linkData['checkoutUrl'],
                    'qr_code'        => $linkData['qrCode'] ?? null,
                    'charge_amount'  => $chargeAmount,
                    'don_hang'       => $donHang,
                ]);
            }

            // Nếu thanh toán qua VNPAY Sandbox -> Tạo URL thanh toán chuẩn HMAC-SHA512
            if ($phuongThuc === 'vnpay') {
                $txnRef    = 'VNPAY_' . time() . '_' . $donHang->id;
                $vnpAmount = (int) round($tongThanhToan);

                $vnpRes = $vnPayService->createPaymentUrl([
                    'txnRef'    => $txnRef,
                    'amount'    => $vnpAmount,
                    'orderInfo' => 'Thanh toan don hang ' . $maDonHang,
                    'ipAddr'    => $request->ip() ?: '127.0.0.1',
                ]);

                if (!$vnpRes['success']) {
                    DB::rollBack();

                    return response()->json([
                        'status'  => false,
                        'message' => $vnpRes['message'] ?? 'Không thể khởi tạo liên kết thanh toán VNPAY.',
                    ], 422);
                }

                ThanhToan::create([
                    'don_hang_id'      => $donHang->id,
                    'ma_giao_dich'     => $txnRef,
                    'cong_thanh_toan'  => 'vnpay',
                    'so_tien'          => $vnpAmount,
                    'loai_tien'        => 'VND',
                    'trang_thai'       => 'pending',
                    'ma_phan_hoi_cong' => 'PENDING',
                    'du_lieu_phan_hoi' => [
                        'vnp_TxnRef' => $txnRef,
                        'vnp_Amount' => $vnpAmount * 100,
                    ],
                ]);

                $this->clearPurchasedCart($cartIdsToRemove, $validUserId, $sessionId);

                DB::commit();

                return response()->json([
                    'status'         => true,
                    'payment_method' => 'vnpay',
                    'message'        => 'Khởi tạo đơn hàng & cổng thanh toán VNPAY Sandbox thành công!',
                    'txn_ref'        => $txnRef,
                    'payment_url'    => $vnpRes['payment_url'],
                    'don_hang'       => $donHang,
                ]);
            }

            // Thanh toán COD
            ThanhToan::create([
                'don_hang_id'      => $donHang->id,
                'ma_giao_dich'     => 'COD_' . time() . '_' . $donHang->id,
                'cong_thanh_toan'  => 'cod',
                'so_tien'          => $tongThanhToan,
                'loai_tien'        => 'VND',
                'trang_thai'       => 'pending',
                'ma_phan_hoi_cong' => null,
                'du_lieu_phan_hoi' => null,
            ]);

            $this->clearPurchasedCart($cartIdsToRemove, $validUserId, $sessionId);

            DB::commit();

            return response()->json([
                'status'         => true,
                'payment_method' => 'cod',
                'message'        => 'Đặt hàng COD thành công! Mã đơn hàng của bạn là #' . $donHang->ma_don_hang,
                'don_hang'       => $donHang,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi xử lý đơn hàng: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function clearPurchasedCart(array $cartIdsToRemove, ?int $validUserId, ?string $sessionId): void
    {
        if (!empty($cartIdsToRemove)) {
            GioHang::whereIn('id', $cartIdsToRemove)->delete();
            return;
        }

        if ($validUserId) {
            GioHang::where('nguoi_dung_id', $validUserId)->delete();
        } elseif ($sessionId) {
            GioHang::where('session_id', $sessionId)->delete();
        }
    }

    public function getDonHangClient(Request $request)
    {
        $userId    = $request->get('user_id') ?: $request->get('nguoi_dung_id');
        $phone     = trim((string) $request->get('phone', ''));
        $searchKey = trim((string) $request->get('search', ''));
        $status    = $request->get('status');

        $query = DonHang::query();

        // 1. Lọc theo Người dùng hoặc Số điện thoại
        if ($userId) {
            $query->where(function ($q) use ($userId, $phone) {
                $q->where('nguoi_dung_id', $userId);
                if ($phone) {
                    $q->orWhere('so_dien_thoai_nhan', $phone);
                }
            });
        } elseif ($phone) {
            $query->where('so_dien_thoai_nhan', $phone);
        }

        // 2. Lọc theo trạng thái đơn hàng (nếu có chọn tab)
        if ($status && $status !== 'all') {
            $query->where('trang_thai_don_hang', $status);
        }

        // 3. Tìm kiếm theo mã đơn hàng hoặc tên người nhận
        if ($searchKey) {
            $query->where(function ($q) use ($searchKey) {
                $q->where('ma_don_hang', 'LIKE', "%{$searchKey}%")
                  ->orWhere('ho_ten_nguoi_nhan', 'LIKE', "%{$searchKey}%")
                  ->orWhere('so_dien_thoai_nhan', 'LIKE', "%{$searchKey}%");
            });
        }

        $orders = $query->with([
            'items.variant.product',
            'payments' => function ($q) {
                $q->orderBy('id', 'desc');
            },
            'serials'
        ])
        ->orderBy('id', 'desc')
        ->get();

        $formatted = $orders->map(function ($order) {
            $items = $order->items->map(function ($item) {
                $image = null;
                if ($item->variant) {
                    $image = $item->variant->hinh_anh ?: ($item->variant->product ? ($item->variant->product->anh_chinh ?: $item->variant->product->hinh_anh) : null);
                }
                return [
                    'id'               => $item->id,
                    'bien_the_id'      => $item->bien_the_id,
                    'ten_san_pham'     => $item->ten_san_pham,
                    'ten_bien_the'     => $item->ten_bien_the,
                    'so_luong'         => (int) $item->so_luong,
                    'don_gia'          => (float) $item->don_gia,
                    'thanh_tien'       => (float) $item->thanh_tien,
                    'danh_sach_serial' => $item->danh_sach_serial ?: [],
                    'hinh_anh'         => $image ?: '/laptop-placeholder.png',
                ];
            });

            // Danh sách Serial/IMEI đã gán cho đơn hàng
            $serials = $order->serials ? $order->serials->map(function ($s) {
                return [
                    'so_serial'     => $s->so_serial,
                    'imei'          => $s->imei,
                    'han_bao_hanh'  => $s->han_bao_hanh ? (is_string($s->han_bao_hanh) ? $s->han_bao_hanh : $s->han_bao_hanh->format('d/m/Y')) : null,
                    'ngay_xuat_kho' => $s->ngay_xuat_kho ? (is_string($s->ngay_xuat_kho) ? $s->ngay_xuat_kho : $s->ngay_xuat_kho->format('d/m/Y')) : null,
                ];
            }) : [];

            return [
                'id'                     => $order->id,
                'ma_don_hang'            => $order->ma_don_hang ?: ('DH' . $order->id),
                'ngay_dat'               => $order->ngay_dat ? $order->ngay_dat->format('d/m/Y H:i') : ($order->created_at ? $order->created_at->format('d/m/Y H:i') : ''),
                'ho_ten_nguoi_nhan'      => $order->ho_ten_nguoi_nhan,
                'so_dien_thoai_nhan'     => $order->so_dien_thoai_nhan,
                'dia_chi_giao_hang'      => $order->dia_chi_giao_hang,
                'ghi_chu'                => $order->ghi_chu,
                'tong_tien_hang'         => (float) $order->tong_tien_hang,
                'so_tien_giam'           => (float) $order->so_tien_giam,
                'phi_van_chuyen'         => (float) $order->phi_van_chuyen,
                'tong_thanh_toan'        => (float) $order->tong_thanh_toan,
                'phuong_thuc_thanh_toan' => $order->phuong_thuc_thanh_toan,
                'trang_thai_thanh_toan'  => $order->trang_thai_thanh_toan,
                'trang_thai_don_hang'    => $order->trang_thai_don_hang,
                'items'                  => $items,
                'serials'                => $serials,
                'payments'               => $order->payments,
                'can_cancel'             => in_array($order->trang_thai_don_hang, ['cho_xac_nhan', 'pending']),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách đơn hàng client thành công',
            'data'    => $formatted,
        ]);
    }

    public function huyDonHangClient(Request $request)
    {
        $id     = $request->input('id');
        $lyDo   = trim((string) $request->input('ly_do', 'Khách hàng yêu cầu hủy đơn'));
        $userId = $request->input('user_id');

        $query = DonHang::where('id', $id);
        if ($userId) {
            $query->where('nguoi_dung_id', $userId);
        }
        $donHang = $query->first();

        if (!$donHang) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy đơn hàng cần hủy.',
            ], 404);
        }

        if (!in_array($donHang->trang_thai_don_hang, ['cho_xac_nhan', 'pending'])) {
            return response()->json([
                'status'  => false,
                'message' => 'Đơn hàng đã được nhân viên tiếp nhận hoặc đang giao, không thể tự hủy. Vui lòng liên hệ tổng đài 1900.6789 để được hỗ trợ!',
            ], 400);
        }

        $donHang->trang_thai_don_hang = 'da_huy';
        $donHang->ghi_chu = trim(($donHang->ghi_chu ? $donHang->ghi_chu . ' | ' : '') . '[Khách hủy]: ' . $lyDo);
        $donHang->save();

        return response()->json([
            'status'  => true,
            'message' => 'Hủy đơn hàng #' . ($donHang->ma_don_hang ?: $donHang->id) . ' thành công!',
            'data'    => $donHang,
        ]);
    }
}
