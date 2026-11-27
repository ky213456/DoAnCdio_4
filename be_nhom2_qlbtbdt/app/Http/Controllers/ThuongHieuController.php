<?php

namespace App\Http\Controllers;

use App\Models\ThuongHieu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ThuongHieuController extends Controller
{
    public function getData()
    {
        $data = ThuongHieu::withCount('products')->orderBy('id', 'desc')->get();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu thương hiệu thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_thuong_hieu' => 'required|string|max:120|unique:thuong_hieu,ten_thuong_hieu',
        ], [
            'ten_thuong_hieu.required' => 'Vui lòng nhập tên thương hiệu',
            'ten_thuong_hieu.unique'   => 'Thương hiệu này đã tồn tại',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $th = ThuongHieu::create([
            'ten_thuong_hieu' => trim($request->ten_thuong_hieu),
            'slug'            => Str::slug($request->ten_thuong_hieu) . '-' . rand(10, 99),
            'logo'            => $request->logo,
            'mo_ta'           => $request->mo_ta,
            'trang_thai'      => $request->boolean('trang_thai', true),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm thương hiệu thành công',
            'data'    => $th,
        ], 201);
    }

    public function update(Request $request)
    {
        $th = ThuongHieu::find($request->id);
        if (!$th) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy thương hiệu'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_thuong_hieu' => 'required|string|max:120|unique:thuong_hieu,ten_thuong_hieu,' . $th->id,
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $th->ten_thuong_hieu = trim($request->ten_thuong_hieu);
        if ($request->filled('logo')) $th->logo = $request->logo;
        $th->mo_ta = $request->mo_ta;
        $th->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật thương hiệu thành công',
            'data'    => $th,
        ]);
    }

    public function destroy(Request $request)
    {
        $th = ThuongHieu::find($request->id);
        if (!$th) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy thương hiệu'], 404);
        }
        $th->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa thương hiệu thành công']);
    }

    public function changeStatus(Request $request)
    {
        $th = ThuongHieu::find($request->id);
        if (!$th) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy thương hiệu'], 404);
        }
        $th->trang_thai = !$th->trang_thai;
        $th->save();
        return response()->json([
            'status'     => true,
            'message'    => 'Đổi trạng thái thương hiệu thành công',
            'trang_thai' => $th->trang_thai,
        ]);
    }

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
