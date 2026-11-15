<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DanhMucSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('danh_muc')->truncate();

        $danhMucs = [
            // Danh mục cha (danh_muc_cha_id = null)
            [
                'id' => 1,
                'ten_danh_muc' => 'Laptop',
                'slug' => Str::slug('Laptop'),
                'mo_ta' => 'Máy tính xách tay học tập, văn phòng và gaming cao cấp',
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png',
                'danh_muc_cha_id' => null,
                'thu_tu' => 1,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'ten_danh_muc' => 'Màn hình',
                'slug' => Str::slug('Man hinh'),
                'mo_ta' => 'Màn hình máy tính PC Gaming, Đồ họa chuẩn màu và Văn phòng',
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_10_.png',
                'danh_muc_cha_id' => null,
                'thu_tu' => 2,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'ten_danh_muc' => 'Linh kiện PC',
                'slug' => Str::slug('Linh kien PC'),
                'mo_ta' => 'RAM, SSD, CPU, VGA và linh kiện xây dựng case máy tính',
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/r/a/ram-corsair-vengeance-rgb-ddr5-5200mhz-32gb_3_.png',
                'danh_muc_cha_id' => null,
                'thu_tu' => 3,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'ten_danh_muc' => 'Gaming Gear',
                'slug' => Str::slug('Gaming Gear'),
                'mo_ta' => 'Chuột, Bàn phím cơ, Tai nghe chơi game chuyên nghiệp',
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-gaming-logitech-pro-x-superlight-2-lightspeed-8.png',
                'danh_muc_cha_id' => null,
                'thu_tu' => 4,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Danh mục con (danh_muc_cha_id trỏ vào Linh kiện PC ID = 3)
            [
                'id' => 5,
                'ten_danh_muc' => 'RAM',
                'slug' => Str::slug('RAM'),
                'mo_ta' => 'Bộ nhớ trong cho máy tính để bàn PC và Laptop',
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/r/a/ram-corsair-vengeance-rgb-ddr5-5200mhz-32gb_3_.png',
                'danh_muc_cha_id' => 3,
                'thu_tu' => 1,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'ten_danh_muc' => 'SSD',
                'slug' => Str::slug('SSD'),
                'mo_ta' => 'Ổ cứng thể rắn tốc độ đọc ghi cao NVMe PCIe & SATA',
                'hinh_anh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_8__8_24.png',
                'danh_muc_cha_id' => 3,
                'thu_tu' => 2,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'ten_danh_muc' => 'VGA',
                'slug' => Str::slug('VGA'),
                'mo_ta' => 'Card đồ họa màn hình rời NVIDIA RTX & AMD Radeon',
                'hinh_anh' => 'https://anphat.com.vn/media/product/75_47577_vga_asus_rog_strix_geforce_rtx_4070_ti_super_16gb_gddr6x_oc_edition__3_.jpg',
                'danh_muc_cha_id' => 3,
                'thu_tu' => 3,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'ten_danh_muc' => 'CPU',
                'slug' => Str::slug('CPU'),
                'mo_ta' => 'Bộ vi xử lý trung tâm Intel Core & AMD Ryzen',
                'hinh_anh' => 'https://anphat.com.vn/media/product/46741_intel_core_i7_14700k.jpg',
                'danh_muc_cha_id' => 3,
                'thu_tu' => 4,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('danh_muc')->insert($danhMucs);
    }
}
