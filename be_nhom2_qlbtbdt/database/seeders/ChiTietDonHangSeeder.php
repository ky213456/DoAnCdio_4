<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChiTietDonHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('chi_tiet_don_hang')->delete();

        $chiTiets = [
            // Chi tiết đơn hàng 1: Màn hình LG
            [
                'id' => 1,
                'don_hang_id' => 1,
                'bien_the_id' => 7,
                'ten_san_pham' => 'Màn hình LG UltraGear 27GR75Q-B 27" 2K IPS 165Hz 1ms',
                'ten_bien_the' => 'Tiêu chuẩn 27 inch 2K 165Hz',
                'so_luong' => 1,
                'don_gia' => 5490000.00,
                'thanh_tien' => 5490000.00,
                'danh_sach_serial' => json_encode(['LG-27GR75Q-003']),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],

            // Chi tiết đơn hàng 2: Laptop ROG Strix G16
            [
                'id' => 2,
                'don_hang_id' => 2,
                'bien_the_id' => 1,
                'ten_san_pham' => 'Laptop ASUS ROG Strix G16 G614JV i7-13650HX RTX 4060 165Hz',
                'ten_bien_the' => 'i7-13650HX / RAM 16GB / SSD 512GB / RTX 4060',
                'so_luong' => 1,
                'don_gia' => 35990000.00,
                'thanh_tien' => 35990000.00,
                'danh_sach_serial' => json_encode(['ROG-G16-2026-SN03']),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],

            // Chi tiết đơn hàng 3: Chuột Logitech GPX2
            [
                'id' => 3,
                'don_hang_id' => 3,
                'bien_the_id' => 10,
                'ten_san_pham' => 'Chuột Gaming không dây Logitech G Pro X Superlight 2 White',
                'ten_bien_the' => 'Màu Trắng (White)',
                'so_luong' => 1,
                'don_gia' => 3190000.00,
                'thanh_tien' => 3190000.00,
                'danh_sach_serial' => json_encode(['LOG-GPX2-WH-001']),
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],

            // Chi tiết đơn hàng 4: SSD Samsung 990 Pro 1TB
            [
                'id' => 4,
                'don_hang_id' => 4,
                'bien_the_id' => 3,
                'ten_san_pham' => 'Ổ cứng SSD Samsung 990 Pro 1TB PCIe Gen 4.0 NVMe M.2 2280',
                'ten_bien_the' => 'Dung lượng 1TB',
                'so_luong' => 1,
                'don_gia' => 2490000.00,
                'thanh_tien' => 2490000.00,
                'danh_sach_serial' => json_encode(['SAMS-990P-1002']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('chi_tiet_don_hang')->insert($chiTiets);
    }
}
