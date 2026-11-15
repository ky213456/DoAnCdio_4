<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\KhachHang;
use App\Models\SanPham;
use Illuminate\Http\Request;

class ThongKeController extends Controller
{
    public function kpiDashboard()
    {
        $tong_don_hang   = DonHang::count();
        $tong_doanh_thu  = DonHang::sum('tong_thanh_toan');
        $tong_khach_hang = KhachHang::where('vai_tro', 'customer')->count();
        $tong_san_pham   = SanPham::count();

        // Đơn hàng gần nhất kèm thông tin khách
        $don_hang_moi = DonHang::leftJoin('nguoi_dung', 'don_hang.nguoi_dung_id', '=', 'nguoi_dung.id')
            ->select(
                'don_hang.*',
                'nguoi_dung.ho_ten as ten_khach_hang'
            )
            ->orderBy('don_hang.id', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu thống kê KPI thành công',
            'data'    => [
                'tong_don_hang'   => $tong_don_hang,
                'tong_doanh_thu'  => $tong_doanh_thu,
                'tong_khach_hang' => $tong_khach_hang,
                'tong_san_pham'   => $tong_san_pham,
                'don_hang_moi'    => $don_hang_moi,
            ],
        ]);
    }
}
