<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GioHangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('gio_hang')->truncate();

        $gioHangs = [
            // Khách hàng Nguyễn Văn A (ID 3) có trong giỏ RAM Corsair Black (bien_the_id = 5)
            [
                'id' => 1,
                'nguoi_dung_id' => 3,
                'session_id' => null,
                'bien_the_id' => 5,
                'so_luong' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Khách hàng Trần Thị B (ID 4) có trong giỏ Chuột Logitech Đen (bien_the_id = 11)
            [
                'id' => 2,
                'nguoi_dung_id' => 4,
                'session_id' => null,
                'bien_the_id' => 11,
                'so_luong' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Khách hàng Lê Hoàng Nam (ID 5) có trong giỏ Màn hình Dell U2724D (bien_the_id = 8)
            [
                'id' => 3,
                'nguoi_dung_id' => 5,
                'session_id' => null,
                'bien_the_id' => 8,
                'so_luong' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Khách vãng lai (chưa đăng nhập) duyệt web
            [
                'id' => 4,
                'nguoi_dung_id' => null,
                'session_id' => 'guest_session_xyz789',
                'bien_the_id' => 4, // SSD Samsung 2TB
                'so_luong' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('gio_hang')->insert($gioHangs);
    }
}
