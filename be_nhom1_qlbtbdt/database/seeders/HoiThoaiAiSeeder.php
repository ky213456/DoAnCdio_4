<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoiThoaiAiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hoi_thoai_ai')->truncate();

        $hoiThoais = [
            // Cuộc hội thoại 1: Khách hàng Nguyễn Văn A hỏi tư vấn Laptop & Màn hình
            [
                'id' => 1,
                'nguoi_dung_id' => 3,
                'session_id' => 'ai_session_user3_001',
                'tieu_de' => 'Tư vấn mua Laptop Gaming và Màn hình đồ họa',
                'ngu_canh' => 'Khách hàng có ngân sách khoảng 35 - 40 triệu, cần tìm thiết bị chơi game nặng và thiết kế đồ họa.',
                'trang_thai' => true,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],

            // Cuộc hội thoại 2: Khách hàng Trần Thị B hỏi tư vấn nâng cấp linh kiện SSD & RAM
            [
                'id' => 2,
                'nguoi_dung_id' => 4,
                'session_id' => 'ai_session_user4_002',
                'tieu_de' => 'Tư vấn nâng cấp linh kiện SSD & RAM DDR5',
                'ngu_canh' => 'Khách hàng muốn tăng tốc độ máy tính bàn PC, ưu tiên tốc độ đọc ghi cao và có đèn LED RGB.',
                'trang_thai' => true,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
        ];

        DB::table('hoi_thoai_ai')->insert($hoiThoais);
    }
}
