<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SerialSanPhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('serial_san_pham')->truncate();

        $serials = [
            // Serial cho Màn hình LG (bien_the_id = 7)
            [
                'bien_the_id' => 7,
                'don_hang_id' => null,
                'so_serial' => 'LG-27GR75Q-001',
                'imei' => null,
                'trang_thai' => 'trong_kho',
                'han_bao_hanh' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'bien_the_id' => 7,
                'don_hang_id' => null,
                'so_serial' => 'LG-27GR75Q-002',
                'imei' => null,
                'trang_thai' => 'trong_kho',
                'han_bao_hanh' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'bien_the_id' => 7,
                'don_hang_id' => 1, // Đã bán cho Đơn hàng 1
                'so_serial' => 'LG-27GR75Q-003',
                'imei' => null,
                'trang_thai' => 'da_ban',
                'han_bao_hanh' => now()->addMonths(24),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(4)
            ],

            // Serial cho Laptop ROG Strix G16 (bien_the_id = 1)
            [
                'bien_the_id' => 1,
                'don_hang_id' => null,
                'so_serial' => 'ROG-G16-2026-SN01',
                'imei' => null,
                'trang_thai' => 'trong_kho',
                'han_bao_hanh' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'bien_the_id' => 1,
                'don_hang_id' => null,
                'so_serial' => 'ROG-G16-2026-SN02',
                'imei' => null,
                'trang_thai' => 'trong_kho',
                'han_bao_hanh' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'bien_the_id' => 1,
                'don_hang_id' => 2, // Đã bán cho Đơn hàng 2
                'so_serial' => 'ROG-G16-2026-SN03',
                'imei' => null,
                'trang_thai' => 'da_ban',
                'han_bao_hanh' => now()->addMonths(24),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(2)
            ],

            // Serial cho SSD Samsung 990 Pro 1TB (bien_the_id = 3)
            [
                'bien_the_id' => 3,
                'don_hang_id' => null,
                'so_serial' => 'SAMS-990P-1001',
                'imei' => null,
                'trang_thai' => 'trong_kho',
                'han_bao_hanh' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'bien_the_id' => 3,
                'don_hang_id' => 4, // Bán cho Đơn hàng 4
                'so_serial' => 'SAMS-990P-1002',
                'imei' => null,
                'trang_thai' => 'da_ban',
                'han_bao_hanh' => now()->addMonths(60),
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Serial cho RAM Corsair Black (bien_the_id = 5)
            [
                'bien_the_id' => 5,
                'don_hang_id' => null,
                'so_serial' => 'COR-VEN-B-001',
                'imei' => null,
                'trang_thai' => 'trong_kho',
                'han_bao_hanh' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],

            // Serial cho Chuột Logitech GPX2 Trắng (bien_the_id = 10)
            [
                'bien_the_id' => 10,
                'don_hang_id' => 3, // Bán cho Đơn hàng 3
                'so_serial' => 'LOG-GPX2-WH-001',
                'imei' => null,
                'trang_thai' => 'da_ban',
                'han_bao_hanh' => now()->addMonths(24),
                'created_at' => now()->subDays(1),
                'updated_at' => now()
            ],
            [
                'bien_the_id' => 10,
                'don_hang_id' => null,
                'so_serial' => 'LOG-GPX2-WH-002',
                'imei' => null,
                'trang_thai' => 'trong_kho',
                'han_bao_hanh' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        DB::table('serial_san_pham')->insert($serials);
    }
}
