<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HinhAnhSanPhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('hinh_anh_san_pham')->delete();

        $hinhAnhs = [
            // SP 1: Màn hình LG UltraGear
            ['san_pham_id' => 1, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_10_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            ['san_pham_id' => 1, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_10_.png', 'thu_tu' => 2, 'la_anh_chinh' => false, 'created_at' => now(), 'updated_at' => now()],

            // SP 2: Màn hình Dell UltraSharp
            ['san_pham_id' => 2, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-dell-ultrasharp-u2724d-27-inch_5_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            ['san_pham_id' => 2, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-dell-ultrasharp-u2724d-27-inch_5_.png', 'thu_tu' => 2, 'la_anh_chinh' => false, 'created_at' => now(), 'updated_at' => now()],

            // SP 3: Màn hình ASUS OLED
            ['san_pham_id' => 3, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/7/h732_21_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            ['san_pham_id' => 3, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/7/h732_21_.png', 'thu_tu' => 2, 'la_anh_chinh' => false, 'created_at' => now(), 'updated_at' => now()],

            // SP 4: SSD Samsung 990 Pro
            ['san_pham_id' => 4, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_8__8_24.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 5: SSD Kingston NV2
            ['san_pham_id' => 5, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:58:58/q:90/plain/https://cellphones.com.vn/media/catalog/product/1/_/1_tb_3_1.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 6: RAM Corsair Vengeance
            ['san_pham_id' => 6, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/r/a/ram-corsair-vengeance-rgb-ddr5-5200mhz-32gb_3_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 7: RAM G.Skill Trident Z5
            ['san_pham_id' => 7, 'duong_dan_anh' => 'https://product.hstatic.net/200000722513/product/t-z5-rgb-32gb-2x16gb-5600-ddr5-silver_127969cd4ae24b288cd6aefbd8e5d9e3_0745f4da09da450aac57f829f4f71dd4.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 8: Laptop ROG Strix G16
            ['san_pham_id' => 8, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 9: Laptop Dell XPS 15
            ['san_pham_id' => 9, 'duong_dan_anh' => 'https://cdn.tgdd.vn/Products/Images/44/314837/dell-xps-15-9530-i7-71015716-2.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 10: VGA ASUS RTX 4070 Ti SUPER
            ['san_pham_id' => 10, 'duong_dan_anh' => 'https://anphat.com.vn/media/product/75_47577_vga_asus_rog_strix_geforce_rtx_4070_ti_super_16gb_gddr6x_oc_edition__3_.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 11: CPU Intel i7-14700K
            ['san_pham_id' => 11, 'duong_dan_anh' => 'https://anphat.com.vn/media/product/46741_intel_core_i7_14700k.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 12: Chuột Logitech GPX2
            ['san_pham_id' => 12, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-gaming-logitech-pro-x-superlight-2-lightspeed-8.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 13: Bàn phím Akko 3087
            ['san_pham_id' => 13, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/b/a/ban-phim-co-akko-3087-rf-one-piece-luffy-cream-4_1.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 14: Bàn phím Corsair K70
            ['san_pham_id' => 14, 'duong_dan_anh' => 'https://anphat.com.vn/media/product/41888_corsair_k70_rgb_pro__1_.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 15: Tai nghe HyperX Cloud II
            ['san_pham_id' => 15, 'duong_dan_anh' => 'https://pcmarket.vn/media/product/3534_71qv_nf8djl__sl1500_.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],

            // SP 16: MacBook Pro 14 M3 Pro (18GB/512GB) Space Black
            ['san_pham_id' => 16, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/i/m/image_1396_1.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 17: MacBook Air 13 M2 (8GB/256GB) Midnight
            ['san_pham_id' => 17, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:250:250/q:100/plain/https://cellphones.com.vn/media/catalog/product/m/a/macbook_air_m2_1.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 18: Laptop Lenovo Legion Pro 5 16IRX9 i7-14650HX RTX 4060
            ['san_pham_id' => 18, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:250:250/q:100/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_509_36_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 19: Laptop Lenovo ThinkPad E14 Gen 5 Core i5-1335U
            ['san_pham_id' => 19, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_945_1__7.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 20: Laptop MSI Katana 15 B13VFK i7-13620H RTX 4060
            ['san_pham_id' => 20, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_509_30_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 21: Laptop Acer Predator Helios Neo 16 i7-14700HX RTX 4070
            ['san_pham_id' => 21, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 22: Laptop HP Victus 16 Core i7-13700H RTX 4050
            ['san_pham_id' => 22, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_d_i_1__4_87.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 23: Laptop ASUS Zenbook 14 OLED UX3405 Intel Core Ultra 7
            ['san_pham_id' => 23, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_659_9.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 24: Màn hình Samsung Odyssey OLED G9 49" Cong 240Hz 0.03ms
            ['san_pham_id' => 24, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-cong-gaming-samsung-odyssey-g9-lc49g95tssexxv-49-inch_1__2.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 25: Màn hình Samsung ViewFinity S9 5K 27" Chuyên Đồ Họa
            ['san_pham_id' => 25, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:358:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/g/r/group_183_1_4.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 26: Màn hình LG DualUp 28MQ780 28" Tỷ lệ 16:18 IPS Ergo
            ['san_pham_id' => 26, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_10_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 27: Màn hình Gigabyte M27Q 27" 2K 170Hz SuperSpeed IPS
            ['san_pham_id' => 27, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/7/h732_21_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 28: Màn hình ViewSonic Omni VX2728J-2K 27" 180Hz Fast-IPS
            ['san_pham_id' => 28, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-dell-ultrasharp-u2724d-27-inch_5_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 29: CPU AMD Ryzen 7 7800X3D (Up to 5.0GHz, 8 Nhân 16 Luồng, 3D V-Cache)
            ['san_pham_id' => 29, 'duong_dan_anh' => 'https://anphat.com.vn/media/product/46741_intel_core_i7_14700k.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 30: CPU Intel Core i9-14900K (Up to 6.0GHz, 24 Nhân 32 Luồng)
            ['san_pham_id' => 30, 'duong_dan_anh' => 'https://anphat.com.vn/media/product/46741_intel_core_i7_14700k.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 31: Card đồ họa MSI GeForce RTX 4070 Ti SUPER 16G Ventus 3X
            ['san_pham_id' => 31, 'duong_dan_anh' => 'https://anphat.com.vn/media/product/75_47577_vga_asus_rog_strix_geforce_rtx_4070_ti_super_16gb_gddr6x_oc_edition__3_.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 32: Card đồ họa Gigabyte GeForce RTX 4060 EAGLE OC 8G
            ['san_pham_id' => 32, 'duong_dan_anh' => 'https://anphat.com.vn/media/product/75_47577_vga_asus_rog_strix_geforce_rtx_4070_ti_super_16gb_gddr6x_oc_edition__3_.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 33: Ổ cứng SSD Samsung 980 1TB NVMe M.2 2280 PCIe Gen 3
            ['san_pham_id' => 33, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:58:58/q:90/plain/https://cellphones.com.vn/media/catalog/product/1/_/1_tb_3_1.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 34: RAM Kingston Fury Beast RGB 32GB (2x16GB) DDR5 5600MHz
            ['san_pham_id' => 34, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/r/a/ram-corsair-vengeance-rgb-ddr5-5200mhz-32gb_3_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 35: Chuột ASUS ROG Harpe Ace Aim Lab Edition Không Dây
            ['san_pham_id' => 35, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-gaming-logitech-pro-x-superlight-2-lightspeed-8.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            // SP 36: Bàn phím cơ không dây Logitech MX Mechanical Mini
            ['san_pham_id' => 36, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/b/a/ban-phim-co-akko-3087-rf-one-piece-luffy-cream-4_1.jpg', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('hinh_anh_san_pham')->insert($hinhAnhs);
    }
}
