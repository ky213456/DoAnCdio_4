<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DanhGiaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('danh_gia')->truncate();

        $danhGias = [
            // Đánh giá 1: Khách hàng Nguyễn Văn A đánh giá Màn hình LG (SP 1) từ Đơn hàng 1
            [
                'id' => 1,
                'san_pham_id' => 1,
                'nguoi_dung_id' => 3, // Nguyễn Văn A
                'don_hang_id' => 1,   // Đơn hàng 1
                'so_sao' => 5,
                'noi_dung' => 'Màn hình màu sắc rất đẹp, tần số quét 165Hz chơi game mượt mà không bị xé hình. Đóng gói rất kỹ, cực kỳ đáng tiền!',
                'hinh_anh_dinh_kem' => json_encode([
                    'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500'
                ]),
                'da_mua_hang' => true,
                'trang_thai_duyet' => true,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],

            // Đánh giá 2: Khách hàng Trần Thị B đánh giá Laptop ROG Strix G16 (SP 8) từ Đơn hàng 2
            [
                'id' => 2,
                'san_pham_id' => 8,
                'nguoi_dung_id' => 4, // Trần Thị B
                'don_hang_id' => 2,   // Đơn hàng 2
                'so_sao' => 5,
                'noi_dung' => 'Máy thiết kế đẹp hầm hố chuẩn ROG, chiến mượt mà mọi game nặng, hệ thống 3 quạt tản nhiệt chạy êm mát. Shop giao hàng cực nhanh!',
                'hinh_anh_dinh_kem' => json_encode([
                    'https://images.unsplash.com/photo-1603302576837-37561b2e2302?w=500'
                ]),
                'da_mua_hang' => true,
                'trang_thai_duyet' => true,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],

            // Đánh giá 3: Khách hàng Nguyễn Văn A đánh giá Chuột Logitech GPX2 (SP 12) từ Đơn hàng 3
            [
                'id' => 3,
                'san_pham_id' => 12,
                'nguoi_dung_id' => 3, // Nguyễn Văn A
                'don_hang_id' => 3,   // Đơn hàng 3
                'so_sao' => 5,
                'noi_dung' => 'Chuột siêu nhẹ chỉ 60g, cầm vẩy Valorant cực kỳ chuẩn xác, pin dùng cả tuần chưa phải sạc lại. Rất hài lòng.',
                'hinh_anh_dinh_kem' => null,
                'da_mua_hang' => true,
                'trang_thai_duyet' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Đánh giá 4: Khách hàng Lê Hoàng Nam đánh giá SSD Samsung 990 Pro (SP 4) từ Đơn hàng 4
            [
                'id' => 4,
                'san_pham_id' => 4,
                'nguoi_dung_id' => 5, // Lê Hoàng Nam
                'don_hang_id' => 4,   // Đơn hàng 4
                'so_sao' => 5,
                'noi_dung' => 'Tốc độ đọc ghi cực khủng hơn 7000MB/s, cài Windows và load phần mềm nặng chỉ trong chớp mắt. Hàng chính hãng tem đầy đủ.',
                'hinh_anh_dinh_kem' => null,
                'da_mua_hang' => true,
                'trang_thai_duyet' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('danh_gia')->insert($danhGias);
    }
}
