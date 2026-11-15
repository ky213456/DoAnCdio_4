<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Request;

class SanPhamController extends Controller
{
    // ==========================================
    // 1. ADMIN - QUẢN LÝ SẢN PHẨM
    // ==========================================
    public function getData()
    {
        // SQL JOIN: Lấy thông tin sản phẩm cùng tên danh mục và thương hiệu
        $data = SanPham::join('danh_muc', 'san_pham.danh_muc_id', '=', 'danh_muc.id')
            ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->select(
                'san_pham.*',
                'danh_muc.ten_danh_muc',
                'thuong_hieu.ten_thuong_hieu'
            )
            ->orderBy('san_pham.id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu sản phẩm thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Thêm sản phẩm thành công'
        ]);
    }

    public function update(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật sản phẩm thành công'
        ]);
    }

    public function destroy(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Xóa sản phẩm thành công'
        ]);
    }

    public function changeStatus(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Đổi trạng thái sản phẩm thành công'
        ]);
    }

    // ==========================================
    // 2. CLIENT - SẢN PHẨM KHÁCH HÀNG
    // ==========================================
    public function getDataClient()
    {
        // SQL JOIN: Lấy danh sách sản phẩm đang mở bán (trang_thai = 1)
        $data = SanPham::join('danh_muc', 'san_pham.danh_muc_id', '=', 'danh_muc.id')
            ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->where('san_pham.trang_thai', 1)
            ->select(
                'san_pham.*',
                'danh_muc.ten_danh_muc',
                'thuong_hieu.ten_thuong_hieu'
            )
            ->orderBy('san_pham.id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu sản phẩm client thành công',
            'data'    => $data,
        ]);
    }

    public function filterClient(Request $request)
    {
        $query = SanPham::join('danh_muc', 'san_pham.danh_muc_id', '=', 'danh_muc.id')
            ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->where('san_pham.trang_thai', 1);

        if ($request->has('danh_muc_id') && $request->danh_muc_id) {
            $query->where('san_pham.danh_muc_id', $request->danh_muc_id);
        }

        if ($request->has('thuong_hieu_id') && $request->thuong_hieu_id) {
            $query->where('san_pham.thuong_hieu_id', $request->thuong_hieu_id);
        }

        if ($request->has('gia_min') && $request->gia_min) {
            $query->where('san_pham.gia_goc', '>=', $request->gia_min);
        }

        if ($request->has('gia_max') && $request->gia_max) {
            $query->where('san_pham.gia_goc', '<=', $request->gia_max);
        }

        $data = $query->select(
            'san_pham.*',
            'danh_muc.ten_danh_muc',
            'thuong_hieu.ten_thuong_hieu'
        )->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lọc sản phẩm client thành công',
            'data'    => $data,
        ]);
    }

    public function chiTietSanPham($id)
    {
        $data = SanPham::join('danh_muc', 'san_pham.danh_muc_id', '=', 'danh_muc.id')
            ->join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->where('san_pham.id', $id)
            ->select(
                'san_pham.*',
                'danh_muc.ten_danh_muc',
                'thuong_hieu.ten_thuong_hieu'
            )
            ->first();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy chi tiết sản phẩm thành công',
            'data'    => $data,
        ]);
    }

    public function compareClient(Request $request)
    {
        $ids = $request->get('ids', []);
        $data = SanPham::whereIn('id', $ids)->get();

        return response()->json([
            'status'  => true,
            'message' => 'So sánh sản phẩm thành công',
            'data'    => $data,
        ]);
    }
}
