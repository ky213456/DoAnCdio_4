<?php

namespace App\Http\Controllers;

use App\Models\ThuongHieu;
use Illuminate\Http\Request;

class ThuongHieuController extends Controller
{
    // ==========================================
    // 1. ADMIN - QUẢN LÝ THƯƠNG HIỆU
    // ==========================================
    public function getData()
    {
        $data = ThuongHieu::all();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu thương hiệu thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Thêm thương hiệu thành công'
        ]);
    }

    public function update(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thương hiệu thành công'
        ]);
    }

    public function destroy(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Xóa thương hiệu thành công'
        ]);
    }

    public function changeStatus(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Đổi trạng thái thương hiệu thành công'
        ]);
    }

    // ==========================================
    // 2. CLIENT - THƯƠNG HIỆU KHÁCH HÀNG
    // ==========================================
    public function getDataClient()
    {
        $data = ThuongHieu::where('trang_thai', 1)->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách thương hiệu client thành công',
            'data'    => $data,
        ]);
    }
}
