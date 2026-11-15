<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThongSoKyThuatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thong_so_ky_thuat')->truncate();

        $thongSoKyThuat = [
            // SP 1: Màn hình LG UltraGear 27GR75Q-B
            [
                'san_pham_id' => 1,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => '27 inch QHD (2560 x 1440) Fast IPS',
                'do_phan_giai' => '2560 x 1440',
                'tan_so_quet' => '165Hz',
                'pin' => null,
                'trong_luong' => '6.19 kg',
                'cong_ket_noi' => '2x HDMI 2.0, 1x DisplayPort 1.4, 1x Headphone Out',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['hdr' => 'HDR10', 'truy_xuat_mau' => '99% sRGB', 'cong_nghe' => 'AMD FreeSync Premium, G-Sync']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 2: Màn hình Dell UltraSharp U2724D
            [
                'san_pham_id' => 2,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => '27 inch 2K IPS Black',
                'do_phan_giai' => '2560 x 1440',
                'tan_so_quet' => '120Hz',
                'pin' => null,
                'trong_luong' => '6.55 kg',
                'cong_ket_noi' => '1x DisplayPort 1.4, 1x HDMI, 1x Type-C 90W, 4x USB 3.2 Gen 2',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['do_tuong_phan' => '2000:1', 'do_phu_mau' => '100% sRGB, 98% DCI-P3', 'cong_nghe' => 'ComfortView Plus']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 3: Màn hình Gaming ASUS ROG Swift PG27AQDM
            [
                'san_pham_id' => 3,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => '27 inch 2K OLED',
                'do_phan_giai' => '2560 x 1440',
                'tan_so_quet' => '240Hz',
                'pin' => null,
                'trong_luong' => '6.9 kg',
                'cong_ket_noi' => '2x HDMI 2.0, 1x DisplayPort 1.4, 2x USB 3.2 Gen 1',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['thoi_gian_phan_hoi' => '0.03ms GtG', 'do_sang_toi_da' => '1000 nits', 'do_phu_mau' => '99% DCI-P3']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 4: SSD Samsung 990 Pro 1TB
            [
                'san_pham_id' => 4,
                'cpu' => null,
                'ram' => null,
                'o_cung' => '1TB PCIe Gen 4.0 x4 NVMe 2.0',
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '9.0 g',
                'cong_ket_noi' => 'M.2 2280',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['toc_do_doc' => '7450 MB/s', 'toc_do_ghi' => '6900 MB/s', 'chip_nho' => 'Samsung V-NAND TLC']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 5: SSD Kingston NV2 1TB
            [
                'san_pham_id' => 5,
                'cpu' => null,
                'ram' => null,
                'o_cung' => '1TB PCIe 4.0 x4 NVMe',
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '7.0 g',
                'cong_ket_noi' => 'M.2 2280',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['toc_do_doc' => '3500 MB/s', 'toc_do_ghi' => '2100 MB/s', 'tbw' => '320 TBW']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 6: RAM Corsair Vengeance RGB 32GB DDR5
            [
                'san_pham_id' => 6,
                'cpu' => null,
                'ram' => '32GB (2x16GB) DDR5 6000MHz CL30',
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '120 g',
                'cong_ket_noi' => 'DIMM 288-pin',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['led' => 'RGB 10 vùng tùy biến iCUE', 'ho_tro_ep_xung' => 'Intel XMP 3.0 & AMD EXPO']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 7: RAM G.Skill Trident Z5 RGB 32GB DDR5
            [
                'san_pham_id' => 7,
                'cpu' => null,
                'ram' => '32GB (2x16GB) DDR5 6400MHz CL32',
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '150 g',
                'cong_ket_noi' => 'DIMM 288-pin',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['led' => 'RGB đồng bộ bo mạch chủ', 'chat_lieu' => 'Nhôm phay xước cao cấp']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 8: Laptop ASUS ROG Strix G16
            [
                'san_pham_id' => 8,
                'cpu' => 'Intel Core i7-13650HX (14 nhân, 20 luồng, up to 4.9GHz)',
                'ram' => '16GB DDR5 4800MHz (2 khe, nâng cấp tối đa 32GB)',
                'o_cung' => '512GB SSD PCIe 4.0 NVMe M.2',
                'card_do_hoa' => 'NVIDIA GeForce RTX 4060 8GB GDDR6 (TGP 140W)',
                'man_hinh' => '16.0 inch FHD+ (1920 x 1200) IPS 165Hz 100% sRGB',
                'do_phan_giai' => '1920 x 1200 (16:10)',
                'tan_so_quet' => '165Hz',
                'pin' => '90WHrs, 4-cell Li-ion',
                'trong_luong' => '2.50 kg',
                'cong_ket_noi' => '1x Thunderbolt 4, 1x USB 3.2 Type-C, 2x USB 3.2 Type-A, 1x HDMI 2.1, 1x LAN RJ45',
                'he_dieu_hanh' => 'Windows 11 Home',
                'thong_so_them' => json_encode(['tan_nhiet' => '3 quạt tản nhiệt ROG Intelligent Cooling', 'ban_phim' => 'RGB 4 vùng']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 9: Laptop Dell XPS 15 9530
            [
                'san_pham_id' => 9,
                'cpu' => 'Intel Core i7-13700H (14 nhân, 20 luồng, up to 5.0GHz)',
                'ram' => '16GB DDR5 4800MHz',
                'o_cung' => '512GB SSD M.2 PCIe NVMe',
                'card_do_hoa' => 'Intel Iris Xe Graphics',
                'man_hinh' => '15.6 inch 3.5K (3456 x 2160) OLED Touch 400 nits',
                'do_phan_giai' => '3456 x 2160 (OLED Touch)',
                'tan_so_quet' => '60Hz',
                'pin' => '86WHrs',
                'trong_luong' => '1.92 kg',
                'cong_ket_noi' => '2x Thunderbolt 4, 1x USB-C 3.2 Gen 2, 1x SD Card, 1x 3.5mm Audio',
                'he_dieu_hanh' => 'Windows 11 Home',
                'thong_so_them' => json_encode(['chat_lieu' => 'Vỏ nhôm CNC nguyên khối & Sợi Carbon', 'am_thanh' => 'Hệ thống 4 loa Waves Nx 3D']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 10: VGA ASUS ROG Strix RTX 4070 Ti SUPER 16GB
            [
                'san_pham_id' => 10,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => 'GeForce RTX 4070 Ti SUPER 16GB GDDR6X',
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '1.80 kg',
                'cong_ket_noi' => '2x HDMI 2.1a, 3x DisplayPort 1.4a',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['chuan_giao_tiep' => 'PCIe 4.0', 'nguon_khuyen_nghi' => '750W', 'dau_cap_nguon' => '1x 16-pin']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 11: CPU Intel Core i7-14700K
            [
                'san_pham_id' => 11,
                'cpu' => 'Intel Core i7-14700K (20 nhân, 28 luồng, up to 5.6GHz)',
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => 'Intel UHD Graphics 770',
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '80 g',
                'cong_ket_noi' => 'Socket LGA 1700',
                'he_dieu_hanh' => null,
                'thong_so_them' => json_encode(['xung_nhip' => '3.4GHz - 5.6GHz', 'bo_nho_dem' => '33MB Intel Smart Cache', 'tdp' => '125W']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 12: Chuột Logitech G Pro X Superlight 2
            [
                'san_pham_id' => 12,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => 'Pin sạc lên đến 95 giờ',
                'trong_luong' => '60 g',
                'cong_ket_noi' => 'LIGHTSPEED Wireless, Type-C',
                'he_dieu_hanh' => 'Windows, macOS',
                'thong_so_them' => json_encode(['cam_bien' => 'HERO 2 32,000 DPI', 'switch' => 'Quang học lai LIGHTFORCE']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 13: Bàn phím cơ AKKO 3087 RF White & Cyan
            [
                'san_pham_id' => 13,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => 'Pin AAA tiện lợi',
                'trong_luong' => '950 g',
                'cong_ket_noi' => 'Dây USB Type-C tháo rời & Wireless 2.4GHz',
                'he_dieu_hanh' => 'Windows, macOS, Linux',
                'thong_so_them' => json_encode(['switch' => 'Akko CS Switch V3 Haze Pink', 'keycap' => 'PBT Double-Shot OEM Profile']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 14: Bàn phím cơ Corsair K70 RGB PRO
            [
                'san_pham_id' => 14,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '1.15 kg',
                'cong_ket_noi' => 'Dây cáp bện Type-C tháo rời',
                'he_dieu_hanh' => 'Windows, macOS',
                'thong_so_them' => json_encode(['switch' => 'CHERRY MX Red', 'polling_rate' => '8,000Hz công nghệ AXON', 'khung' => 'Nhôm phay xước']),
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // SP 15: Tai nghe Gaming Kingston HyperX Cloud II Red
            [
                'san_pham_id' => 15,
                'cpu' => null,
                'ram' => null,
                'o_cung' => null,
                'card_do_hoa' => null,
                'man_hinh' => null,
                'do_phan_giai' => null,
                'tan_so_quet' => null,
                'pin' => null,
                'trong_luong' => '320 g',
                'cong_ket_noi' => 'Jack 3.5mm, USB Audio Control Box (Virtual 7.1)',
                'he_dieu_hanh' => 'PC, PS5, Xbox, Mobile',
                'thong_so_them' => json_encode(['driver' => '53mm Dynamic Neodymium', 'microphone' => 'Khử tiếng ồn có thể tháo rời']),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('thong_so_ky_thuat')->insert($thongSoKyThuat);
    }
}
