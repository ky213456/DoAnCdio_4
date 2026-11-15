<?php

namespace App\Http\Controllers;

use App\Models\SerialSanPham;
use Illuminate\Http\Request;

class BaoHanhController extends Controller
{
    public function traCuu(Request $request)
    {
        $serial = $request->get('serial');

        if (!$serial) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng cung cấp số Serial hoặc mã IMEI'
            ], 400);
        }

        // SQL JOIN: Tra cứu bảo hành từ số Serial liên kết biến thể, sản phẩm và đơn mua
        $data = SerialSanPham::join('bien_the_san_pham', 'serial_san_pham.bien_the_id', '=', 'bien_the_san_pham.id')
            ->join('san_pham', 'bien_the_san_pham.san_pham_id', '=', 'san_pham.id')
            ->leftJoin('don_hang', 'serial_san_pham.don_hang_id', '=', 'don_hang.id')
            ->where(function ($query) use ($serial) {
                $query->where('serial_san_pham.so_serial', $serial)
                      ->orWhere('serial_san_pham.imei', $serial);
            })
            ->select(
                'serial_san_pham.id',
                'serial_san_pham.so_serial',
                'serial_san_pham.imei',
                'serial_san_pham.trang_thai as trang_thai_may',
                'serial_san_pham.ngay_xuat_kho',
                'serial_san_pham.han_bao_hanh',
                'san_pham.ten_san_pham',
                'san_pham.thoi_gian_bao_hanh',
                'bien_the_san_pham.ten_bien_the',
                'don_hang.ma_don_hang'
            )
            ->first();

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'Không tìm thấy thông tin bảo hành cho Serial/IMEI này'
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Tra cứu dữ liệu bảo hành thành công',
            'data'    => $data,
        ]);
    }
}
