<?php

namespace App\Http\Controllers;

use App\Models\DanhMuc;
use App\Models\SanPham;
use App\Models\ThuongHieu;
use Illuminate\Http\Request;

class TrangChuController extends Controller
{
    public function getData()
    {
        // 1. Danh mục nổi bật
        $danh_muc = DanhMuc::where('trang_thai', 1)->orderBy('thu_tu', 'asc')->get();

        // 2. Thương hiệu chính hãng
        $thuong_hieu = ThuongHieu::where('trang_thai', 1)->get();

        // 3. Sản phẩm Flash Sale (đa dạng ngành hàng: Laptop, VGA, Màn hình, Gear, SSD...)
        $flash_sale = SanPham::join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->where('san_pham.trang_thai', 1)
            ->whereNotNull('san_pham.gia_khuyen_mai')
            ->select(
                'san_pham.*',
                'thuong_hieu.ten_thuong_hieu'
            )
            // Sắp xếp xen kẽ danh mục: Laptop (1), VGA (7), Màn hình (2), Gear (4), CPU (8), RAM (5), SSD (6)
            ->orderByRaw("FIELD(san_pham.danh_muc_id, 1, 7, 4, 8, 2, 5, 6)")
            ->take(8)
            ->get();

        // 4. Sản phẩm bán chạy / nổi bật (đa dạng ngành hàng)
        $san_pham_noi_bat = SanPham::join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->where('san_pham.trang_thai', 1)
            ->where('san_pham.noi_bat', 1)
            ->select(
                'san_pham.*',
                'thuong_hieu.ten_thuong_hieu'
            )
            ->orderByRaw("FIELD(san_pham.danh_muc_id, 1, 4, 7, 8, 2, 6, 5)")
            ->take(8)
            ->get();

        // 5. Toàn bộ sản phẩm đầy đủ để Frontend có thể hiển thị theo Tab danh mục
        $tat_ca_san_pham = SanPham::join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->where('san_pham.trang_thai', 1)
            ->select(
                'san_pham.*',
                'thuong_hieu.ten_thuong_hieu'
            )
            ->get();

        return response()->json([
            'status'           => true,
            'message'          => 'Lấy dữ liệu trang chủ thành công',
            'data'             => [
                'danh_muc'         => $danh_muc,
                'thuong_hieu'      => $thuong_hieu,
                'flash_sale'       => $flash_sale,
                'san_pham_noi_bat' => $san_pham_noi_bat,
                'tat_ca_san_pham'  => $tat_ca_san_pham,
            ],
            // Hỗ trợ truy cập trực tiếp các key theo convention
            'data_danh_muc'    => $danh_muc,
            'data_thuong_hieu' => $thuong_hieu,
            'data_flash_sale'  => $flash_sale,
            'data_sp_noi_bat'  => $san_pham_noi_bat,
            'list_san_pham'    => $tat_ca_san_pham,
        ]);
    }

    // Function lấy tất cả sản phẩm
    public function getTatCaSanPham()
    {
        $data = SanPham::join('thuong_hieu', 'san_pham.thuong_hieu_id', '=', 'thuong_hieu.id')
            ->join('danh_muc', 'san_pham.danh_muc_id', '=', 'danh_muc.id')
            ->where('san_pham.trang_thai', 1)
            ->select(
                'san_pham.*',
                'thuong_hieu.ten_thuong_hieu',
                'danh_muc.ten_danh_muc'
            )
            ->orderBy('san_pham.id', 'asc')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Lấy tất cả sản phẩm thành công',
            'data'    => $data,
        ]);
    }
}
