<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaGiamGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ma_giam_gia')->delete();
        
        $maGiamGias = [
            // 1. Mã giảm giá theo phần trăm %
            [
                'id' => 1,
                'ma_code' => 'WELCOME100',
                'tieu_de' => 'Giảm 10% cho thành viên mới',
                'loai_giam_gia' => 'percent',
                'gia_tri_giam' => 10.00, // 10%
                'giam_toi_da' => 200000.00, // Giảm tối đa 200k
                'don_hang_toi_thieu' => 500000.00,
                'so_luong' => 100,
                'da_su_dung' => 12,
                'ngay_bat_dau' => now()->subDays(10),
                'ngay_ket_thuc' => now()->addDays(30),
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 2. Mã giảm giá số tiền cố định (fixed)
            [
                'id' => 2,
                'ma_code' => 'SALEOFF50K',
                'tieu_de' => 'Giảm thẳng 50.000đ cho đơn từ 1 Triệu',
                'loai_giam_gia' => 'fixed',
                'gia_tri_giam' => 50000.00,
                'giam_toi_da' => 50000.00,
                'don_hang_toi_thieu' => 1000000.00,
                'so_luong' => 50,
                'da_su_dung' => 5,
                'ngay_bat_dau' => now()->subDays(10),
                'ngay_ket_thuc' => now()->addDays(15),
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 3. Mã ưu đãi dành riêng cho sản phẩm Gaming
            [
                'id' => 3,
                'ma_code' => 'GAMINGGEAR',
                'tieu_de' => 'Ưu đãi 15% khi mua Phụ kiện & Gaming Gear',
                'loai_giam_gia' => 'percent',
                'gia_tri_giam' => 15.00,
                'giam_toi_da' => 500000.00,
                'don_hang_toi_thieu' => 2000000.00,
                'so_luong' => 30,
                'da_su_dung' => 8,
                'ngay_bat_dau' => now()->subDays(10),
                'ngay_ket_thuc' => now()->addDays(45),
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // 4. Mã siêu giảm giá mua Laptop / PC
            [
                'id' => 4,
                'ma_code' => 'VIPLAPTOP',
                'tieu_de' => 'Giảm ngay 1.000.000đ cho đơn hàng Laptop trên 20 Triệu',
                'loai_giam_gia' => 'fixed',
                'gia_tri_giam' => 1000000.00,
                'giam_toi_da' => 1000000.00,
                'don_hang_toi_thieu' => 20000000.00,
                'so_luong' => 20,
                'da_su_dung' => 2,
                'ngay_bat_dau' => now()->subDays(10),
                'ngay_ket_thuc' => now()->addDays(60),
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('ma_giam_gia')->insert($maGiamGias);
    }
}
