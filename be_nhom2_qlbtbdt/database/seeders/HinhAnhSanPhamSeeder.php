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
        DB::table('hinh_anh_san_pham')->truncate();

        $hinhAnhs = [
            // SP 1: Màn hình LG UltraGear
            ['san_pham_id' => 1, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_10_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            ['san_pham_id' => 1, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_11_.png', 'thu_tu' => 2, 'la_anh_chinh' => false, 'created_at' => now(), 'updated_at' => now()],

            // SP 2: Màn hình Dell UltraSharp
            ['san_pham_id' => 2, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-dell-ultrasharp-u2724d-27-inch_5_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            ['san_pham_id' => 2, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-dell-ultrasharp-u2724d-27-inch_6_.png', 'thu_tu' => 2, 'la_anh_chinh' => false, 'created_at' => now(), 'updated_at' => now()],

            // SP 3: Màn hình ASUS OLED
            ['san_pham_id' => 3, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/7/h732_21_.png', 'thu_tu' => 1, 'la_anh_chinh' => true, 'created_at' => now(), 'updated_at' => now()],
            ['san_pham_id' => 3, 'duong_dan_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/7/h732_22_.png', 'thu_tu' => 2, 'la_anh_chinh' => false, 'created_at' => now(), 'updated_at' => now()],

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
        ];

        DB::table('hinh_anh_san_pham')->insert($hinhAnhs);
    }
}
