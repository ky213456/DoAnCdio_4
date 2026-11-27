<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function getData()
    {
        $data = Voucher::orderBy('id', 'desc')->get();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách Voucher thành công',
            'data'    => $data,
        ]);
    }

    public function addData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ma_code'       => 'required|string|max:50|unique:ma_giam_gia,ma_code',
            'tieu_de'       => 'required|string|max:255',
            'loai_giam_gia' => 'required|in:percent,fixed',
            'gia_tri_giam'  => 'required|numeric|min:1',
            'so_luong'      => 'required|integer|min:1',
        ], [
            'ma_code.required' => 'Vui lòng nhập mã Voucher',
            'ma_code.unique'   => 'Mã Voucher này đã tồn tại',
            'tieu_de.required' => 'Vui lòng nhập tiêu đề chương trình',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $voucher = Voucher::create([
            'ma_code'            => strtoupper(trim($request->ma_code)),
            'tieu_de'            => trim($request->tieu_de),
            'loai_giam_gia'      => $request->loai_giam_gia,
            'gia_tri_giam'       => (float) $request->gia_tri_giam,
            'giam_toi_da'        => $request->filled('giam_toi_da') ? (float) $request->giam_toi_da : null,
            'don_hang_toi_thieu' => (float) ($request->don_hang_toi_thieu ?? 0),
            'so_luong'           => (int) $request->so_luong,
            'da_su_dung'         => 0,
            'ngay_bat_dau'       => $request->ngay_bat_dau ?: now(),
            'ngay_ket_thuc'      => $request->ngay_ket_thuc ?: now()->addDays(30),
            'trang_thai'         => $request->boolean('trang_thai', true),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thêm Voucher thành công',
            'data'    => $voucher,
        ], 201);
    }

    public function update(Request $request)
    {
        $voucher = Voucher::find($request->id);
        if (!$voucher) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy Voucher'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ma_code'       => 'required|string|max:50|unique:ma_giam_gia,ma_code,' . $voucher->id,
            'tieu_de'       => 'required|string|max:255',
            'loai_giam_gia' => 'required|in:percent,fixed',
            'gia_tri_giam'  => 'required|numeric|min:1',
            'so_luong'      => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $voucher->update([
            'ma_code'            => strtoupper(trim($request->ma_code)),
            'tieu_de'            => trim($request->tieu_de),
            'loai_giam_gia'      => $request->loai_giam_gia,
            'gia_tri_giam'       => (float) $request->gia_tri_giam,
            'giam_toi_da'        => $request->filled('giam_toi_da') ? (float) $request->giam_toi_da : null,
            'don_hang_toi_thieu' => (float) ($request->don_hang_toi_thieu ?? 0),
            'so_luong'           => (int) $request->so_luong,
            'ngay_bat_dau'       => $request->ngay_bat_dau ?: $voucher->ngay_bat_dau,
            'ngay_ket_thuc'      => $request->ngay_ket_thuc ?: $voucher->ngay_ket_thuc,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật Voucher thành công',
            'data'    => $voucher,
        ]);
    }

    public function destroy(Request $request)
    {
        $voucher = Voucher::find($request->id);
        if (!$voucher) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy Voucher'], 404);
        }
        $voucher->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa Voucher thành công']);
    }

    public function changeStatus(Request $request)
    {
        $voucher = Voucher::find($request->id);
        if (!$voucher) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy Voucher'], 404);
        }
        $voucher->trang_thai = !$voucher->trang_thai;
        $voucher->save();
        return response()->json([
            'status'     => true,
            'message'    => 'Đổi trạng thái Voucher thành công',
            'trang_thai' => $voucher->trang_thai,
        ]);
    }
}
