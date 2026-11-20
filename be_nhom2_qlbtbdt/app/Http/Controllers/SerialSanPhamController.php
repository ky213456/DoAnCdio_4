<?php

namespace App\Http\Controllers;

use App\Models\SerialSanPham;
use App\Models\BienTheSanPham;
use App\Models\SanPham;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SerialSanPhamController extends Controller
{
    // ==========================================
    // 1. DANH SÁCH SERIAL & THỐNG KÊ KHO (UC08)
    // ==========================================
    public function getData(Request $request)
    {
        $status    = $request->get('trang_thai', 'all');
        $search    = trim((string) $request->get('search', ''));
        $bienTheId = $request->get('bien_the_id');

        $query = SerialSanPham::join('bien_the_san_pham', 'serial_san_pham.bien_the_id', '=', 'bien_the_san_pham.id')
            ->join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id')
            ->leftJoin('don_hang', 'serial_san_pham.don_hang_id', '=', 'don_hang.id')
            ->select(
                'serial_san_pham.*',
                'san_pham.ten_san_pham',
                'san_pham.anh_chinh as hinh_anh_sp',
                'san_pham.thoi_gian_bao_hanh',
                'bien_the_san_pham.ten_bien_the',
                'bien_the_san_pham.sku',
                'don_hang.ma_don_hang',
                'don_hang.ho_ten_nguoi_nhan',
                'don_hang.so_dien_thoai_nhan'
            );

        if ($status && $status !== 'all') {
            $query->where('serial_san_pham.trang_thai', $status);
        }

        if ($bienTheId) {
            $query->where('serial_san_pham.bien_the_id', $bienTheId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('serial_san_pham.so_serial', 'LIKE', "%{$search}%")
                  ->orWhere('serial_san_pham.imei', 'LIKE', "%{$search}%")
                  ->orWhere('san_pham.ten_san_pham', 'LIKE', "%{$search}%")
                  ->orWhere('don_hang.ma_don_hang', 'LIKE', "%{$search}%");
            });
        }

        $serials = $query->orderBy('serial_san_pham.id', 'desc')->get();

        // Tính toán thống kê KPI kho Serial
        $stats = [
            'total'       => SerialSanPham::count(),
            'trong_kho'   => SerialSanPham::where('trang_thai', 'trong_kho')->count(),
            'da_ban'      => SerialSanPham::where('trang_thai', 'da_ban')->count(),
            'giu_cho'     => SerialSanPham::where('trang_thai', 'giu_cho')->count(),
            'loi'         => SerialSanPham::where('trang_thai', 'loi')->count(),
        ];

        // Lấy danh sách biến thể để phục vụ dropdown nhập lô
        $variants = BienTheSanPham::join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id')
            ->select(
                'bien_the_san_pham.id',
                'bien_the_san_pham.sku',
                'bien_the_san_pham.ten_bien_the',
                'bien_the_san_pham.so_luong_ton',
                'san_pham.ten_san_pham'
            )
            ->get();

        return response()->json([
            'status'   => true,
            'message'  => 'Lấy dữ liệu kho serial thành công',
            'stats'    => $stats,
            'data'     => $serials,
            'variants' => $variants,
        ]);
    }

    // ==========================================
    // 2. NHẬP LÔ SERIAL / IMEI VÀO KHO (UC08)
    // ==========================================
    public function nhapLo(Request $request)
    {
        $bienTheId = $request->input('bien_the_id');
        $rawSerials = $request->input('serials'); // Array hoặc chuỗi nhiều dòng
        $ghiChu = $request->input('ghi_chu', 'Nhập kho lô mới');

        if (!$bienTheId || empty($rawSerials)) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng chọn biến thể sản phẩm và cung cấp ít nhất 1 mã Serial!',
            ], 422);
        }

        $variant = BienTheSanPham::find($bienTheId);
        if (!$variant) {
            return response()->json([
                'status'  => false,
                'message' => 'Biến thể sản phẩm không tồn tại.',
            ], 404);
        }

        // Chuẩn hóa mảng danh sách serial
        $serialList = [];
        if (is_array($rawSerials)) {
            $serialList = $rawSerials;
        } else {
            // Tách theo dòng hoặc dấu phẩy
            $lines = preg_split('/[\r\n,]+/', (string) $rawSerials);
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if ($trimmed !== '') {
                    $serialList[] = $trimmed;
                }
            }
        }

        if (empty($serialList)) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy mã Serial hợp lệ nào.',
            ], 422);
        }

        // Lọc trùng trong danh sách gửi lên
        $serialList = array_unique($serialList);

        // Kiểm tra xem có mã serial nào đã tồn tại trong CSDL chưa
        $existing = SerialSanPham::whereIn('so_serial', $serialList)->pluck('so_serial')->toArray();
        if (!empty($existing)) {
            return response()->json([
                'status'  => false,
                'message' => 'Các mã Serial sau đã tồn tại trong hệ thống: ' . implode(', ', array_slice($existing, 0, 5)) . (count($existing) > 5 ? '...' : ''),
                'duplicates' => $existing,
            ], 422);
        }

        DB::beginTransaction();
        try {
            $insertedCount = 0;
            $now = now();

            foreach ($serialList as $code) {
                SerialSanPham::create([
                    'bien_the_id'   => $bienTheId,
                    'don_hang_id'   => null,
                    'so_serial'     => $code,
                    'imei'          => null,
                    'trang_thai'    => 'trong_kho',
                    'ngay_nhap_kho' => $now,
                    'ghi_chu'       => $ghiChu,
                ]);
                $insertedCount++;
            }

            // Tự động tăng tồn kho cho biến thể
            $variant->increment('so_luong_ton', $insertedCount);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => "Nhập kho thành công {$insertedCount} thiết bị Serial vào biến thể {$variant->ten_bien_the}!",
                'count'   => $insertedCount,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi nhập kho Serial: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ==========================================
    // 3. LẤY DANH SÁCH SERIAL SẴN SÀNG THEO BIẾN THỂ (FIFO)
    // ==========================================
    public function getAvailableByVariant($variantId)
    {
        $serials = SerialSanPham::where('bien_the_id', $variantId)
            ->where('trang_thai', 'trong_kho')
            ->orderBy('id', 'asc') // FIFO: máy nhập trước xuất trước
            ->select('id', 'so_serial', 'imei', 'ngay_nhap_kho', 'trang_thai')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $serials,
        ]);
    }

    // ==========================================
    // 4. GÁN MÃ SERIAL VÀO ĐƠN HÀNG KHI XUẤT KHO (UC09)
    // ==========================================
    public function ganSerialDonHang(Request $request)
    {
        $donHangId    = $request->input('don_hang_id');
        $assignments  = $request->input('assignments', []); // [ { item_id: 1, serial_ids: [10, 11] } ]
        $newStatus    = $request->input('trang_thai_don_hang', 'dang_giao');

        $donHang = DonHang::with(['items.variant.product'])->find($donHangId);
        if (!$donHang) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy đơn hàng #' . $donHangId,
            ], 404);
        }

        if (empty($assignments)) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng chọn mã Serial vật lý để gán vào các mặt hàng trong đơn!',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $totalAssigned = 0;
            $allAssignedSerials = [];

            foreach ($assignments as $asgn) {
                $itemId    = $asgn['item_id'] ?? null;
                $serialIds = $asgn['serial_ids'] ?? [];

                if (!$itemId || empty($serialIds)) continue;

                $item = ChiTietDonHang::where('id', $itemId)->where('don_hang_id', $donHang->id)->first();
                if (!$item) continue;

                // Kiểm tra số lượng serial có khớp số lượng mua không
                if (count($serialIds) > $item->so_luong) {
                    throw new \Exception("Mặt hàng '{$item->ten_san_pham}' chỉ mua {$item->so_luong} sản phẩm nhưng bạn chọn gán " . count($serialIds) . " serial!");
                }

                // Lấy thời gian bảo hành của sản phẩm (tháng)
                $variant = BienTheSanPham::with('product')->find($item->bien_the_id);
                $warrantyMonths = 24;
                if ($variant && $variant->product && $variant->product->thoi_gian_bao_hanh) {
                    $warrantyMonths = (int) $variant->product->thoi_gian_bao_hanh;
                }

                $assignedCodes = [];

                foreach ($serialIds as $sId) {
                    $serial = SerialSanPham::find($sId);
                    if (!$serial) continue;

                    // Chỉ cho phép gán nếu serial đang ở trong kho HOẶC đã thuộc về đơn này
                    if ($serial->trang_thai !== 'trong_kho' && $serial->don_hang_id != $donHang->id) {
                        throw new \Exception("Mã Serial '{$serial->so_serial}' không ở trạng thái sẵn sàng xuất bán (trạng thái: {$serial->trang_thai})!");
                    }

                    $serial->trang_thai    = 'da_ban';
                    $serial->don_hang_id   = $donHang->id;
                    $serial->ngay_xuat_kho = now();
                    $serial->han_bao_hanh  = now()->addMonths($warrantyMonths)->toDateString();
                    $serial->save();

                    $assignedCodes[] = $serial->so_serial;
                    $allAssignedSerials[] = $serial->so_serial;
                    $totalAssigned++;
                }

                // Cập nhật danh sách serial vào chi tiết đơn hàng
                $item->danh_sach_serial = $assignedCodes;
                $item->save();
            }

            // Cập nhật trạng thái đơn hàng
            if ($newStatus) {
                $donHang->trang_thai_don_hang = $newStatus;
            }
            $donHang->save();

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => "Đã gán thành công {$totalAssigned} mã Serial/IMEI và xuất kho đơn hàng #{$donHang->ma_don_hang}!",
                'serials'  => $allAssignedSerials,
                'don_hang' => $donHang->fresh(['items', 'serials']),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Lỗi khi gán Serial xuất kho: ' . $e->getMessage(),
            ], 422);
        }
    }

    // ==========================================
    // 5. ĐỔI TRẠNG THÁI SERIAL (BẢO HÀNH / LỖI KHO)
    // ==========================================
    public function changeStatus(Request $request)
    {
        $id     = $request->input('id');
        $status = $request->input('trang_thai');
        $note   = $request->input('ghi_chu');

        $serial = SerialSanPham::find($id);
        if (!$serial) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy Serial!'], 404);
        }

        if (!in_array($status, ['trong_kho', 'giu_cho', 'da_ban', 'loi'])) {
            return response()->json(['status' => false, 'message' => 'Trạng thái không hợp lệ!'], 422);
        }

        $serial->trang_thai = $status;
        if ($note !== null) {
            $serial->ghi_chu = $note;
        }
        $serial->save();

        return response()->json([
            'status'  => true,
            'message' => "Cập nhật Serial #{$serial->so_serial} sang trạng thái {$status} thành công!",
            'data'    => $serial,
        ]);
    }
}
