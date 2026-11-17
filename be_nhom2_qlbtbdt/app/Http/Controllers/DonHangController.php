<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use Illuminate\Http\Request;

class DonHangController extends Controller
{
    // ==========================================
    // 1. ADMIN - QUẢN LÝ ĐƠN HÀNG
    // ==========================================
    public function getData()
    {
        // SQL JOIN: Lấy thông tin đơn hàng cùng thông tin khách hàng và mã giảm giá (cột trong DB là ma_code)
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
    // 2. CLIENT - ĐƠN HÀNG KHÁCH HÀNG
    // ==========================================
    public function checkout(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Khởi tạo đơn hàng thành công'
        ]);
    }

    public function getDonHangClient(Request $request)
    {
        $userId = $request->get('user_id');

        $data = DonHang::where('nguoi_dung_id', $userId)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách đơn hàng client thành công',
            'data'    => $data,
        ]);
    }
}
