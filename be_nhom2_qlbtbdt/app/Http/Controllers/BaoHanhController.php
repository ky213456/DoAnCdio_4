<?php

namespace App\Http\Controllers;

use App\Models\PhieuBaoHanh;
use App\Models\SerialSanPham;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
                'don_hang.ma_don_hang',
                'don_hang.ho_ten_nguoi_nhan',
                'don_hang.so_dien_thoai_nhan'
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

    public function getAdminTickets()
    {
        $tickets = PhieuBaoHanh::orderBy('id', 'desc')->get();
        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách phiếu bảo hành & đổi trả thành công',
            'data'    => $tickets,
        ]);
    }

    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_khach_hang' => 'required|string|max:120',
            'ten_thiet_bi'   => 'required|string|max:255',
            'mo_ta_loi'      => 'required|string|min:5',
            'loai_yeu_cau'   => 'required|in:bao_hanh,doi_tra',
        ], [
            'ten_khach_hang.required' => 'Vui lòng nhập tên khách hàng',
            'ten_thiet_bi.required'   => 'Vui lòng nhập tên thiết bị',
            'mo_ta_loi.required'      => 'Vui lòng nhập mô tả lỗi kỹ thuật',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $serialRecord = null;
        if ($request->filled('so_serial')) {
            $serialRecord = SerialSanPham::where('so_serial', trim($request->so_serial))
                ->orWhere('imei', trim($request->so_serial))
                ->first();
        }

        $ticket = PhieuBaoHanh::create([
            'ma_phieu'        => 'WAR-' . rand(1000, 9999),
            'serial_id'       => $serialRecord ? $serialRecord->id : null,
            'so_serial'       => $request->so_serial ? trim($request->so_serial) : null,
            'ten_khach_hang'  => trim($request->ten_khach_hang),
            'so_dien_thoai'   => $request->so_dien_thoai,
            'ten_thiet_bi'    => trim($request->ten_thiet_bi),
            'loai_yeu_cau'    => $request->loai_yeu_cau,
            'mo_ta_loi'       => trim($request->mo_ta_loi),
            'trang_thai'      => 'tiep_nhan',
            'ky_thuat_vien'   => $request->ky_thuat_vien ?: 'Trần Linh (KTV Level 2)',
            'han_tra'         => $request->han_tra ?: now()->addDays(5)->format('Y-m-d'),
            'chi_phi_du_kien' => (float) ($request->chi_phi_du_kien ?? 0),
            'ghi_chu'         => $request->ghi_chu,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Tạo phiếu tiếp nhận bảo hành #' . $ticket->ma_phieu . ' thành công',
            'data'    => $ticket,
        ], 201);
    }

    public function updateTicketStatus(Request $request)
    {
        $ticket = PhieuBaoHanh::find($request->id);
        if (!$ticket) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy phiếu bảo hành'], 404);
        }

        if ($request->filled('trang_thai')) {
            $ticket->trang_thai = $request->trang_thai;
        }
        if ($request->filled('ky_thuat_vien')) {
            $ticket->ky_thuat_vien = $request->ky_thuat_vien;
        }
        if ($request->has('ghi_chu')) {
            $ticket->ghi_chu = $request->ghi_chu;
        }
        $ticket->save();

        return response()->json([
            'status'  => true,
            'message' => 'Cập nhật trạng thái phiếu #' . $ticket->ma_phieu . ' thành công',
            'data'    => $ticket,
        ]);
    }

    public function deleteTicket(Request $request)
    {
        $ticket = PhieuBaoHanh::find($request->id);
        if (!$ticket) {
            return response()->json(['status' => false, 'message' => 'Không tìm thấy phiếu bảo hành'], 404);
        }
        $ticket->delete();
        return response()->json(['status' => true, 'message' => 'Đã xóa phiếu bảo hành']);
    }
}
