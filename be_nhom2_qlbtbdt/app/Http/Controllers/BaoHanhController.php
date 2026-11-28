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

        // Kèm lịch sử phiếu bảo hành / đổi trả của serial này (nếu có)
        $tickets = PhieuBaoHanh::where('so_serial', $data->so_serial)
            ->orWhere('serial_id', $data->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Tra cứu dữ liệu bảo hành thành công',
            'data'    => $data,
            'tickets' => $tickets,
        ]);
    }

    /**
     * Khách hàng gửi yêu cầu Bảo hành / Đổi trả trực tuyến
     */
    public function guiYeuCauClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ten_khach_hang' => 'required|string|max:120',
            'so_dien_thoai'  => 'required|string|max:20',
            'ten_thiet_bi'   => 'required|string|max:255',
            'loai_yeu_cau'   => 'required|in:bao_hanh,doi_tra',
            'mo_ta_loi'      => 'required|string|min:5',
        ], [
            'ten_khach_hang.required' => 'Vui lòng nhập họ tên khách hàng',
            'so_dien_thoai.required'  => 'Vui lòng nhập số điện thoại liên hệ',
            'ten_thiet_bi.required'   => 'Vui lòng nhập tên thiết bị cần bảo hành / đổi trả',
            'loai_yeu_cau.in'         => 'Loại yêu cầu không hợp lệ (bao_hanh hoặc doi_tra)',
            'mo_ta_loi.required'      => 'Vui lòng mô tả tình trạng lỗi hoặc lý do đổi trả',
            'mo_ta_loi.min'           => 'Mô tả tình trạng lỗi tối thiểu 5 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()], 422);
        }

        $serialRecord = null;
        $soSerial = $request->filled('so_serial') ? trim($request->so_serial) : null;

        if ($soSerial) {
            $serialRecord = SerialSanPham::where('so_serial', $soSerial)
                ->orWhere('imei', $soSerial)
                ->first();

            // Kiểm tra xem có phiếu nào đang xử lý (chưa trả hàng) cho serial này không
            $activeTicket = PhieuBaoHanh::where(function ($q) use ($soSerial, $serialRecord) {
                    $q->where('so_serial', $soSerial);
                    if ($serialRecord) {
                        $q->orWhere('serial_id', $serialRecord->id);
                    }
                })
                ->where('trang_thai', '!=', 'da_tra_hang')
                ->first();

            if ($activeTicket) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Thiết bị có Serial ' . $soSerial . ' đang có phiếu #' . $activeTicket->ma_phieu . ' chưa hoàn tất xử lý. Vui lòng theo dõi tiến độ phiếu này!'
                ], 409);
            }
        }

        $ticket = PhieuBaoHanh::create([
            'ma_phieu'        => 'WAR-' . rand(1000, 9999),
            'serial_id'       => $serialRecord ? $serialRecord->id : null,
            'so_serial'       => $soSerial,
            'ten_khach_hang'  => trim($request->ten_khach_hang),
            'so_dien_thoai'   => trim($request->so_dien_thoai),
            'ten_thiet_bi'    => trim($request->ten_thiet_bi),
            'loai_yeu_cau'    => $request->loai_yeu_cau,
            'mo_ta_loi'       => trim($request->mo_ta_loi),
            'trang_thai'      => 'tiep_nhan',
            'ky_thuat_vien'   => 'Chờ phân công KTV',
            'han_tra'         => now()->addDays(5)->format('Y-m-d'),
            'chi_phi_du_kien' => 0,
            'ghi_chu'         => $request->filled('ghi_chu') ? trim($request->ghi_chu) : 'Khách hàng gửi yêu cầu trực tuyến',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Gửi yêu cầu ' . ($request->loai_yeu_cau === 'doi_tra' ? 'đổi trả' : 'bảo hành') . ' thành công! Mã phiếu: #' . $ticket->ma_phieu,
            'data'    => $ticket,
        ], 201);
    }

    /**
     * Khách hàng tra cứu lịch sử & tiến độ các phiếu Bảo hành / Đổi trả đã gửi
     */
    public function lichSuYeuCauClient(Request $request)
    {
        $keyword = trim((string) $request->get('keyword', ''));
        $sdt     = trim((string) $request->get('so_dien_thoai', ''));

        if (!$keyword && !$sdt) {
            return response()->json([
                'status'  => false,
                'message' => 'Vui lòng nhập Số điện thoại, Mã phiếu (WAR-...) hoặc Số Serial để tra cứu',
                'data'    => []
            ], 400);
        }

        $query = PhieuBaoHanh::query();

        if ($keyword) {
            $cleanCode = ltrim($keyword, '#');
            $query->where(function ($q) use ($cleanCode) {
                $q->where('ma_phieu', 'like', '%' . $cleanCode . '%')
                  ->orWhere('so_serial', 'like', '%' . $cleanCode . '%')
                  ->orWhere('so_dien_thoai', 'like', '%' . $cleanCode . '%');
            });
        } elseif ($sdt) {
            $query->where('so_dien_thoai', $sdt);
        }

        $tickets = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy danh sách phiếu bảo hành thành công',
            'data'    => $tickets,
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
