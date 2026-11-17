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
        $tong_doanh_thu  = DonHang::where('trang_thai_thanh_toan', 'da_thanh_toan')
            ->orWhere('trang_thai_don_hang', 'giao_thanh_cong')
            ->sum('tong_thanh_toan');

        if ($tong_doanh_thu == 0) {
            $tong_doanh_thu = DonHang::sum('tong_thanh_toan');
        }

        $tong_khach_hang = KhachHang::where('vai_tro', 'customer')->count();
        if ($tong_khach_hang == 0) {
            $tong_khach_hang = KhachHang::count();
        }
        $tong_san_pham   = SanPham::count();

        // Thống kê phân bổ theo trạng thái đơn hàng
        $don_theo_trang_thai = [
            'cho_xac_nhan'    => DonHang::where('trang_thai_don_hang', 'cho_xac_nhan')->count(),
            'da_xac_nhan'     => DonHang::where('trang_thai_don_hang', 'da_xac_nhan')->count(),
            'dang_dong_goi'   => DonHang::where('trang_thai_don_hang', 'dang_dong_goi')->count(),
            'dang_giao'       => DonHang::where('trang_thai_don_hang', 'dang_giao')->count(),
            'giao_thanh_cong' => DonHang::where('trang_thai_don_hang', 'giao_thanh_cong')->count(),
            'da_huy'          => DonHang::where('trang_thai_don_hang', 'da_huy')->count(),
        ];

        // Đơn hàng gần nhất kèm thông tin khách hàng
        $don_hang_moi = DonHang::leftJoin('nguoi_dung', 'don_hang.nguoi_dung_id', '=', 'nguoi_dung.id')
            ->select(
                'don_hang.*',
                'nguoi_dung.ho_ten as ten_khach_hang',
                'nguoi_dung.email as email_khach_hang',
                'nguoi_dung.so_dien_thoai as sdt_khach_hang'
            )
            ->orderBy('don_hang.id', 'desc')
            ->take(10)
            ->get();

        // Top 5 sản phẩm nổi bật
        $top_san_pham = SanPham::select('id', 'ten_san_pham', 'gia_goc', 'gia_khuyen_mai', 'hinh_anh', 'luot_xem')
            ->orderBy('luot_xem', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy dữ liệu thống kê KPI thành công',
            'data'    => [
                'tong_don_hang'        => $tong_don_hang,
                'tong_doanh_thu'       => $tong_doanh_thu,
                'tong_khach_hang'      => $tong_khach_hang,
                'tong_san_pham'        => $tong_san_pham,
                'don_theo_trang_thai'  => $don_theo_trang_thai,
                'don_hang_moi'         => $don_hang_moi,
                'top_san_pham'         => $top_san_pham,
            ],
        ]);
    }
}
