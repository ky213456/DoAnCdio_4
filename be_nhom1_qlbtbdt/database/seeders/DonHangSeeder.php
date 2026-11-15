<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DonHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('don_hang')->truncate();

        $donHangs = [
            // Đơn hàng 1: Mua Màn hình LG (SP 1, biến thể 7) - Đã giao thành công, thanh toán VNPAY
            [
                'id' => 1,
                'ma_don_hang' => 'DH20261101-0001',
                'nguoi_dung_id' => 3, // Nguyễn Văn A
                'ma_giam_gia_id' => null,
                'ho_ten_nguoi_nhan' => 'Nguyễn Văn A',
                'so_dien_thoai_nhan' => '0912345678',
                'dia_chi_giao_hang' => '123 Đường Hải Phòng, Phường Thạch Thang, Quận Hải Châu, Thành phố Đà Nẵng',
                'ghi_chu' => 'Giao hàng giờ hành chính, gọi trước khi đến 15 phút',
                'tong_tien_hang' => 5490000.00,
                'so_tien_giam' => 0.00,
                'phi_van_chuyen' => 0.00,
                'tong_thanh_toan' => 5490000.00,
                'phuong_thuc_thanh_toan' => 'vnpay',
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'trang_thai_don_hang' => 'giao_thanh_cong',
                'ngay_dat' => now()->subDays(5),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(4),
            ],

            // Đơn hàng 2: Mua Laptop ROG Strix G16 (SP 8, biến thể 1) - Áp voucher VIPLAPTOP giảm 1tr, PayOS
            [
                'id' => 2,
                'ma_don_hang' => 'DH20261102-0002',
                'nguoi_dung_id' => 4, // Trần Thị B
                'ma_giam_gia_id' => 4, // VIPLAPTOP
                'ho_ten_nguoi_nhan' => 'Trần Thị B',
                'so_dien_thoai_nhan' => '0987654321',
                'dia_chi_giao_hang' => '456 Đường Nguyễn Lương Bằng, Phường Hòa Khánh Bắc, Quận Liên Chiểu, Thành phố Đà Nẵng',
                'ghi_chu' => 'Đóng gói cẩn thận giúp mình, quà sinh nhật tặng em trai',
                'tong_tien_hang' => 35990000.00,
                'so_tien_giam' => 1000000.00,
                'phi_van_chuyen' => 0.00,
                'tong_thanh_toan' => 34990000.00,
                'phuong_thuc_thanh_toan' => 'payos',
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'trang_thai_don_hang' => 'giao_thanh_cong',
                'ngay_dat' => now()->subDays(3),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2),
            ],

            // Đơn hàng 3: Mua Chuột Logitech GPX2 (SP 12, biến thể 10) - COD, đang giao
            [
                'id' => 3,
                'ma_don_hang' => 'DH20261103-0003',
                'nguoi_dung_id' => 3, // Nguyễn Văn A
                'ma_giam_gia_id' => 2, // SALEOFF50K
                'ho_ten_nguoi_nhan' => 'Nguyễn Văn A',
                'so_dien_thoai_nhan' => '0912345678',
                'dia_chi_giao_hang' => '123 Đường Hải Phòng, Phường Thạch Thang, Quận Hải Châu, Thành phố Đà Nẵng',
                'ghi_chu' => 'Giao hàng buổi chiều sau 14h',
                'tong_tien_hang' => 3190000.00,
                'so_tien_giam' => 50000.00,
                'phi_van_chuyen' => 30000.00,
                'tong_thanh_toan' => 3170000.00,
                'phuong_thuc_thanh_toan' => 'cod',
                'trang_thai_thanh_toan' => 'chua_thanh_toan',
                'trang_thai_don_hang' => 'dang_giao',
                'ngay_dat' => now()->subDays(1),
                'created_at' => now()->subDays(1),
                'updated_at' => now(),
            ],

            // Đơn hàng 4: Mua SSD Samsung 990 Pro 1TB (SP 4, biến thể 3) - VNPAY, chờ xác nhận
            [
                'id' => 4,
                'ma_don_hang' => 'DH20261103-0004',
                'nguoi_dung_id' => 5, // Lê Hoàng Nam
                'ma_giam_gia_id' => null,
                'ho_ten_nguoi_nhan' => 'Lê Hoàng Nam',
                'so_dien_thoai_nhan' => '0935123987',
                'dia_chi_giao_hang' => '78 Phố Huế, Phường Hàng Bài, Quận Hoàn Kiếm, Thành phố Hà Nội',
                'ghi_chu' => 'Giao hỏa tốc giúp mình nhé',
                'tong_tien_hang' => 2490000.00,
                'so_tien_giam' => 0.00,
                'phi_van_chuyen' => 30000.00,
                'tong_thanh_toan' => 2520000.00,
                'phuong_thuc_thanh_toan' => 'vnpay',
                'trang_thai_thanh_toan' => 'da_thanh_toan',
                'trang_thai_don_hang' => 'cho_xac_nhan',
                'ngay_dat' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('don_hang')->insert($donHangs);
    }
}
