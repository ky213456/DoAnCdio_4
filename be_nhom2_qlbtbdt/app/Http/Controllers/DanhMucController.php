<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;

class DanhMucController extends Controller
{
    // ==========================================
    // 1. ADMIN - QUẢN LÝ DANH MỤC
    // ==========================================
    public function getData()
    {
        $data = DanhMuc::all();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu danh mục thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Thêm danh mục thành công'
        ]);
    }

    public function update(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật danh mục thành công'
        ]);
    }

    public function destroy(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Xóa danh mục thành công'
        ]);
    }

    public function changeStatus(Request $request)
    {
        return response()->json([
            'status'  => true,
            'message' => 'Đổi trạng thái danh mục thành công'
        ]);
    }

    // ==========================================
    // 2. CLIENT - DANH MỤC KHÁCH HÀNG
    // ==========================================
    public function getDataClient()
    {
        $data = DanhMuc::where('trang_thai', 1)->orderBy('thu_tu', 'asc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh mục client thành công',
            'data'    => $data,
        ]);
    }
}
