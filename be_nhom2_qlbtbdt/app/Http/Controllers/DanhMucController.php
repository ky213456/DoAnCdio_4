<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DanhMucController extends Controller
{
    public function getData()
    {
        $data = DanhMuc::withCount('products')->orderBy('thu_tu', 'asc')->orderBy('id', 'desc')->get();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu danh mục thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_danh_muc' => 'required|string|max:120|unique:danh_muc,ten_danh_muc',
        ], [
            'ten_danh_muc.required' => 'Vui lòng nhập tên danh mục',
            'ten_danh_muc.unique'   => 'Danh mục này đã tồn tại',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $dm = DanhMuc::create([
            'ten_danh_muc'    => trim($request->ten_danh_muc),
            'slug'            => Str::slug($request->ten_danh_muc) . '-' . rand(10, 99),
            'mo_ta'           => $request->mo_ta,
            'hinh_anh'        => $request->hinh_anh,
            'danh_muc_cha_id' => $request->danh_muc_cha_id ?: null,
            'thu_tu'          => (int) ($request->thu_tu ?? 0),
            'trang_thai'      => $request->boolean('trang_thai', true),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm danh mục thành công',
            'data'    => $dm,
        ], 201);
    }

    public function update(Request $request)
    {
        $dm = DanhMuc::find($request->id);
        if (!$dm) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy danh mục'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ten_danh_muc' => 'required|string|max:120|unique:danh_muc,ten_danh_muc,' . $dm->id,
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $dm->ten_danh_muc = trim($request->ten_danh_muc);
        $dm->mo_ta        = $request->mo_ta;
        if ($request->filled('hinh_anh')) $dm->hinh_anh = $request->hinh_anh;
        if ($request->has('thu_tu')) $dm->thu_tu = (int) $request->thu_tu;
        $dm->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật danh mục thành công',
            'data'    => $dm,
        ]);
    }

    public function destroy(Request $request)
    {
        $dm = DanhMuc::find($request->id);
        if (!$dm) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy danh mục'], 404);
        }
        $dm->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa danh mục thành công']);
    }

    public function changeStatus(Request $request)
    {
        $dm = DanhMuc::find($request->id);
        if (!$dm) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy danh mục'], 404);
        }
        $dm->trang_thai = !$dm->trang_thai;
        $dm->save();
        return response()->json([
            'status'     => true,
            'message'    => 'Đổi trạng thái danh mục thành công',
            'trang_thai' => $dm->trang_thai,
        ]);
    }

    public function getDataClient()
    {
        $data = DanhMuc::where('trang_thai', 1)->get();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách danh mục client thành công',
            'data'    => $data,
        ]);
    }
}
