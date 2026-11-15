<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BienTheSanPhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('bien_the_san_pham')->truncate();

        $bienThes = [
            // Biến thể SP 8: Laptop ROG Strix G16
            [
                'id' => 1,
                'san_pham_id' => 8,
                'sku' => 'ROG-G16-4060-16G-512G',
                'ten_bien_the' => 'i7-13650HX / RAM 16GB / SSD 512GB / RTX 4060',
                'mau_sac' => 'Eclipse Gray',
                'ram' => '16GB DDR5',
                'o_cung' => '512GB SSD',
                'gia_ban' => 39990000,
                'gia_khuyen_mai' => 35990000,
                'so_luong_ton' => 15,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'san_pham_id' => 8,
                'sku' => 'ROG-G16-4060-32G-1TB',
                'ten_bien_the' => 'i7-13650HX / RAM 32GB / SSD 1TB / RTX 4060',
                'mau_sac' => 'Eclipse Gray',
                'ram' => '32GB DDR5',
                'o_cung' => '1TB SSD',
                'gia_ban' => 43990000,
                'gia_khuyen_mai' => 39990000,
                'so_luong_ton' => 10,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Biến thể SP 4: SSD Samsung 990 Pro
            [
                'id' => 3,
                'san_pham_id' => 4,
                'sku' => 'SSD-SAM-990PRO-1TB',
                'ten_bien_the' => 'Dung lượng 1TB',
                'mau_sac' => 'Đen',
                'ram' => null,
                'o_cung' => '1TB SSD',
                'gia_ban' => 3450000,
                'gia_khuyen_mai' => 2490000,
                'so_luong_ton' => 60,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_8__8_24.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'san_pham_id' => 4,
                'sku' => 'SSD-SAM-990PRO-2TB',
                'ten_bien_the' => 'Dung lượng 2TB',
                'mau_sac' => 'Đen',
                'ram' => null,
                'o_cung' => '2TB SSD',
                'gia_ban' => 5990000,
                'gia_khuyen_mai' => 4590000,
                'so_luong_ton' => 40,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_8__8_24.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Biến thể SP 6: RAM Corsair Vengeance
            [
                'id' => 5,
                'san_pham_id' => 6,
                'sku' => 'RAM-COR-VEN-32G-BLACK',
                'ten_bien_the' => '32GB (2x16GB) DDR5 - Màu Đen',
                'mau_sac' => 'Đen',
                'ram' => '32GB DDR5',
                'o_cung' => null,
                'gia_ban' => 4190000,
                'gia_khuyen_mai' => 2890000,
                'so_luong_ton' => 35,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/r/a/ram-corsair-vengeance-rgb-ddr5-5200mhz-32gb_3_.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'san_pham_id' => 6,
                'sku' => 'RAM-COR-VEN-32G-WHITE',
                'ten_bien_the' => '32GB (2x16GB) DDR5 - Màu Trắng',
                'mau_sac' => 'Trắng',
                'ram' => '32GB DDR5',
                'o_cung' => null,
                'gia_ban' => 4290000,
                'gia_khuyen_mai' => 2990000,
                'so_luong_ton' => 25,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/r/a/ram-corsair-vengeance-rgb-ddr5-5200mhz-32gb_3_.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Biến thể SP 1: Màn hình LG UltraGear 27GR75Q-B
            [
                'id' => 7,
                'san_pham_id' => 1,
                'sku' => 'MAN-LG-27GR75Q-STD',
                'ten_bien_the' => 'Tiêu chuẩn 27 inch 2K 165Hz',
                'mau_sac' => 'Đen viền đỏ',
                'ram' => null,
                'o_cung' => null,
                'gia_ban' => 7290000,
                'gia_khuyen_mai' => 5490000,
                'so_luong_ton' => 50,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_10_.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Biến thể SP 2: Màn hình Dell UltraSharp U2724D
            [
                'id' => 8,
                'san_pham_id' => 2,
                'sku' => 'MAN-DELL-U2724D-STD',
                'ten_bien_the' => 'Tiêu chuẩn 27 inch IPS Black',
                'mau_sac' => 'Bạc Platinum',
                'ram' => null,
                'o_cung' => null,
                'gia_ban' => 11500000,
                'gia_khuyen_mai' => 9890000,
                'so_luong_ton' => 30,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-dell-ultrasharp-u2724d-27-inch_5_.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Biến thể SP 9: Laptop Dell XPS 15 9530
            [
                'id' => 9,
                'san_pham_id' => 9,
                'sku' => 'DELL-XPS15-OLED-512G',
                'ten_bien_the' => 'i7-13700H / 16GB / 512GB / OLED Touch',
                'mau_sac' => 'Platinum Silver',
                'ram' => '16GB DDR5',
                'o_cung' => '512GB SSD',
                'gia_ban' => 53000000,
                'gia_khuyen_mai' => 48990000,
                'so_luong_ton' => 10,
                'hinh_anh' => 'https://cdn.tgdd.vn/Products/Images/44/314837/dell-xps-15-9530-i7-71015716-2.jpg',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Biến thể SP 12: Chuột Logitech G Pro X Superlight 2
            [
                'id' => 10,
                'san_pham_id' => 12,
                'sku' => 'LOG-GPX2-WHITE',
                'ten_bien_the' => 'Màu Trắng (White)',
                'mau_sac' => 'Trắng',
                'ram' => null,
                'o_cung' => null,
                'gia_ban' => 3790000,
                'gia_khuyen_mai' => 3190000,
                'so_luong_ton' => 50,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-gaming-logitech-pro-x-superlight-2-lightspeed-8.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 11,
                'san_pham_id' => 12,
                'sku' => 'LOG-GPX2-BLACK',
                'ten_bien_the' => 'Màu Đen (Black)',
                'mau_sac' => 'Đen',
                'ram' => null,
                'o_cung' => null,
                'gia_ban' => 3790000,
                'gia_khuyen_mai' => 3190000,
                'so_luong_ton' => 30,
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-gaming-logitech-pro-x-superlight-2-lightspeed-8.png',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('bien_the_san_pham')->insert($bienThes);
    }
}
