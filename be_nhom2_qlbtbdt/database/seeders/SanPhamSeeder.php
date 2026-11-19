<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SanPhamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('san_pham')->truncate();

        $sanPhams = [
            // 1. Màn hình LG UltraGear 27GR75Q-B
            [
                'id' => 1,
                'danh_muc_id' => 2, // Màn hình
                'thuong_hieu_id' => 3,   // LG
                'ten_san_pham' => 'Màn hình LG UltraGear 27GR75Q-B 27" 2K IPS 165Hz 1ms',
                'slug' => Str::slug('Man hinh LG UltraGear 27GR75Q-B 27 2K IPS 165Hz 1ms'),
                'ma_san_pham' => 'SP-LG-27GR75Q',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-gaming-lg-ultragear-27gr75q-27-inch_10_.png',
                'gia_goc' => 7290000,
                'gia_khuyen_mai' => 5490000,
                'thoi_gian_bao_hanh' => 24,
                'mo_ta_ngan' => 'Màn hình Gaming LG UltraGear 27GR75Q-B kích thước 27 inch, độ phân giải 2K QHD (2560x1440), tần số quét 165Hz, thời gian phản hồi 1ms GtG, tấm nền Fast-IPS chuẩn màu 99% sRGB.',
                'noi_dung_chi_tiet' => '<p>Màn hình Gaming LG UltraGear 27GR75Q-B mang đến hình ảnh sống động với độ phân giải 2K và tần số quét cao 165Hz.</p>',
                'luot_xem' => 150,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2. Màn hình Dell UltraSharp U2724D
            [
                'id' => 2,
                'danh_muc_id' => 2, // Màn hình
                'thuong_hieu_id' => 6,   // Dell
                'ten_san_pham' => 'Màn hình Dell UltraSharp U2724D 27 inch 2K IPS Black 120Hz',
                'slug' => Str::slug('Man hinh Dell UltraSharp U2724D 27 inch 2K IPS Black 120Hz'),
                'ma_san_pham' => 'SP-DELL-U2724D',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/m/a/man-hinh-dell-ultrasharp-u2724d-27-inch_5_.png',
                'gia_goc' => 11500000,
                'gia_khuyen_mai' => 9890000,
                'thoi_gian_bao_hanh' => 36,
                'mo_ta_ngan' => 'Màn hình chuyên đồ họa cao cấp Dell UltraSharp U2724D với công nghệ tấm nền IPS Black độc quyền cho độ tương phản 2000:1, tần số quét 120Hz mượt mà, độ phủ màu 100% sRGB và 98% DCI-P3.',
                'noi_dung_chi_tiet' => '<p>Dell UltraSharp U2724D là sự lựa chọn hàng đầu cho các nhà sáng tạo nội dung và thiết kế đồ họa chuyên nghiệp.</p>',
                'luot_xem' => 220,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 3. Màn hình ASUS ROG Swift PG27AQDM OLED
            [
                'id' => 3,
                'danh_muc_id' => 2, // Màn hình
                'thuong_hieu_id' => 1,   // ASUS
                'ten_san_pham' => 'Màn hình Gaming ASUS ROG Swift PG27AQDM 27" 2K OLED 240Hz 0.03ms',
                'slug' => Str::slug('Man hinh Gaming ASUS ROG Swift PG27AQDM 27 2K OLED 240Hz 003ms'),
                'ma_san_pham' => 'SP-ASUS-PG27AQDM',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/h/7/h732_21_.png',
                'gia_goc' => 24990000,
                'gia_khuyen_mai' => 21490000,
                'thoi_gian_bao_hanh' => 24,
                'mo_ta_ngan' => 'Siêu phẩm màn hình OLED dành cho game thủ chuyên nghiệp. Kích thước 27 inch 2K, tần số quét 240Hz, phản hồi tức thì 0.03ms, hệ thống tản nhiệt buồng hơi tùy chỉnh chống burn-in hiệu quả.',
                'noi_dung_chi_tiet' => '<p>ASUS ROG Swift PG27AQDM mang đến thời gian đáp ứng siêu nhanh 0.03ms cùng màu đen tuyệt đối của màn OLED đỉnh cao.</p>',
                'luot_xem' => 350,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 4. SSD Samsung 990 Pro 1TB
            [
                'id' => 4,
                'danh_muc_id' => 6, // SSD
                'thuong_hieu_id' => 2,   // Samsung
                'ten_san_pham' => 'Ổ cứng SSD Samsung 990 Pro 1TB PCIe Gen 4.0 NVMe M.2 2280',
                'slug' => Str::slug('O cung SSD Samsung 990 Pro 1TB PCIe Gen 40 NVMe M2 2280'),
                'ma_san_pham' => 'SP-SAM-990PRO',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_8__8_24.png',
                'gia_goc' => 3450000,
                'gia_khuyen_mai' => 2490000,
                'thoi_gian_bao_hanh' => 60,
                'mo_ta_ngan' => 'SSD nhanh nhất chuẩn PCIe Gen 4.0 với tốc độ đọc tuần tự lên đến 7,450MB/s và ghi tuần tự 6,900MB/s. Trang bị chip nhớ V-NAND thế hệ mới, tản nhiệt niken kiểm soát nhiệt độ thông minh.',
                'noi_dung_chi_tiet' => '<p>Samsung 990 Pro mang lại hiệu suất tối đa cho trải nghiệm chơi game và xử lý dữ liệu nặng không độ trễ.</p>',
                'luot_xem' => 420,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 5. SSD Kingston NV2 1TB
            [
                'id' => 5,
                'danh_muc_id' => 6, // SSD
                'thuong_hieu_id' => 5,   // Kingston
                'ten_san_pham' => 'Ổ cứng SSD Kingston NV2 1TB PCIe 4.0 x4 NVMe M.2 2280',
                'slug' => Str::slug('O cung SSD Kingston NV2 1TB PCIe 40 x4 NVMe M2 2280'),
                'ma_san_pham' => 'SP-KGS-NV2',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:58:58/q:90/plain/https://cellphones.com.vn/media/catalog/product/1/_/1_tb_3_1.png',
                'gia_goc' => 1950000,
                'gia_khuyen_mai' => 1590000,
                'thoi_gian_bao_hanh' => 36,
                'mo_ta_ngan' => 'Lựa chọn nâng cấp lưu trữ tối ưu chi phí, tốc độ đọc 3,500MB/s và ghi 2,100MB/s, thiết kế một mặt gọn nhẹ dễ dàng gắn cho cả PC và Laptop.',
                'noi_dung_chi_tiet' => '<p>Ổ cứng SSD Kingston NV2 tối ưu hóa tốc độ khởi động máy và load ứng dụng một cách kinh tế nhất.</p>',
                'luot_xem' => 190,
                'noi_bat' => false,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 6. RAM Corsair Vengeance RGB 32GB DDR5
            [
                'id' => 6,
                'danh_muc_id' => 5, // RAM
                'thuong_hieu_id' => 4,   // Corsair
                'ten_san_pham' => 'RAM Corsair Vengeance RGB 32GB (2x16GB) DDR5 6000MHz Black',
                'slug' => Str::slug('RAM Corsair Vengeance RGB 32GB 2x16GB DDR5 6000MHz Black'),
                'ma_san_pham' => 'SP-COR-VEN32G',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/r/a/ram-corsair-vengeance-rgb-ddr5-5200mhz-32gb_3_.png',
                'gia_goc' => 4190000,
                'gia_khuyen_mai' => 2890000,
                'thoi_gian_bao_hanh' => 36,
                'mo_ta_ngan' => 'Kit RAM DDR5 hiệu năng cao với dải LED RGB 10 bóng siêu sáng tùy biến qua iCUE, hỗ trợ Intel XMP 3.0 và AMD EXPO ép xung ổn định ở tần số 6000MHz CL30.',
                'noi_dung_chi_tiet' => '<p>Corsair Vengeance RGB DDR5 đem đến sức mạnh ép xung và hiệu ứng ánh sáng RGB ấn tượng cho dàn máy.</p>',
                'luot_xem' => 310,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 7. RAM G.Skill Trident Z5 RGB 32GB DDR5
            [
                'id' => 7,
                'danh_muc_id' => 5, // RAM
                'thuong_hieu_id' => 10,  // G.Skill
                'ten_san_pham' => 'RAM G.Skill Trident Z5 RGB 32GB (2x16GB) DDR5 6400MHz Silver',
                'slug' => Str::slug('RAM GSkill Trident Z5 RGB 32GB 2x16GB DDR5 6400MHz Silver'),
                'ma_san_pham' => 'SP-GSK-Z5-32G',
                'anh_chinh' => 'https://product.hstatic.net/200000722513/product/t-z5-rgb-32gb-2x16gb-5600-ddr5-silver_127969cd4ae24b288cd6aefbd8e5d9e3_0745f4da09da450aac57f829f4f71dd4.jpg',
                'gia_goc' => 4200000,
                'gia_khuyen_mai' => 3490000,
                'thoi_gian_bao_hanh' => 36,
                'mo_ta_ngan' => 'Thiết kế giáp tản nhiệt nhôm xước ánh bạc phong cách tương lai, chip nhớ tuyển chọn cho tốc độ cực khủng 6400MHz, tương thích hoàn hảo với bo mạch chủ Z790/X670.',
                'noi_dung_chi_tiet' => '<p>G.Skill Trident Z5 RGB là chuẩn mực của dòng RAM cao cấp với khả năng ép xung vượt trội.</p>',
                'luot_xem' => 180,
                'noi_bat' => false,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 8. Laptop Gaming ASUS ROG Strix G16
            [
                'id' => 8,
                'danh_muc_id' => 1, // Laptop
                'thuong_hieu_id' => 1,   // ASUS
                'ten_san_pham' => 'Laptop ASUS ROG Strix G16 G614JV i7-13650HX RTX 4060 165Hz',
                'slug' => Str::slug('Laptop ASUS ROG Strix G16 G614JV i7 13650HX RTX 4060 165Hz'),
                'ma_san_pham' => 'SP-ROG-G16-4060',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/t/e/text_ng_n_1__1_96_1_1_1_1.png',
                'gia_goc' => 39990000,
                'gia_khuyen_mai' => 5000,
                'thoi_gian_bao_hanh' => 24,
                'mo_ta_ngan' => 'Laptop Gaming đỉnh cao trang bị vi xử lý Intel Core i7-13650HX 14 nhân 20 luồng, Card đồ họa rời NVIDIA GeForce RTX 4060 8GB TGP 140W, RAM 16GB DDR5 4800MHz, SSD 512GB PCIe 4.0.',
                'noi_dung_chi_tiet' => '<p>Chiến mượt mọi tựa game AAA với sức mạnh từ CPU Intel thế hệ 13 và GPU RTX 40 series cùng hệ thống tản nhiệt ba quạt thông minh.</p>',
                'luot_xem' => 520,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 9. Laptop Dell XPS 15 9530
            [
                'id' => 9,
                'danh_muc_id' => 1, // Laptop
                'thuong_hieu_id' => 6,   // Dell
                'ten_san_pham' => 'Laptop Dell XPS 15 9530 Core i7-13700H 15.6" 3.5K OLED Touch',
                'slug' => Str::slug('Laptop Dell XPS 15 9530 Core i7 13700H 156 35K OLED Touch'),
                'ma_san_pham' => 'SP-DELL-XPS15',
                'anh_chinh' => 'https://cdn.tgdd.vn/Products/Images/44/314837/dell-xps-15-9530-i7-71015716-2.jpg',
                'gia_goc' => 53000000,
                'gia_khuyen_mai' => 48990000,
                'thoi_gian_bao_hanh' => 12,
                'mo_ta_ngan' => 'Ultrabook đẳng cấp hàng đầu với vỏ nhôm CNC nguyên khối và chiếu nghỉ tay sợi carbon. Màn hình cảm ứng OLED 3.5K sắc nét, vi xử lý i7-13700H, RAM 16GB, SSD 512GB NVMe.',
                'noi_dung_chi_tiet' => '<p>Dell XPS 15 9530 kết hợp hoàn hảo giữa thiết kế sang trọng, màn hình OLED rực rỡ và hiệu năng xử lý tác vụ mạnh mẽ.</p>',
                'luot_xem' => 410,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 10. VGA ASUS ROG Strix RTX 4070 Ti SUPER 16GB
            [
                'id' => 10,
                'danh_muc_id' => 7, // VGA
                'thuong_hieu_id' => 1,   // ASUS
                'ten_san_pham' => 'VGA ASUS ROG Strix GeForce RTX 4070 Ti SUPER 16GB GDDR6X',
                'slug' => Str::slug('VGA ASUS ROG Strix GeForce RTX 4070 Ti SUPER 16GB GDDR6X'),
                'ma_san_pham' => 'SP-ASUS-4070TIS',
                'anh_chinh' => 'https://anphat.com.vn/media/product/75_47577_vga_asus_rog_strix_geforce_rtx_4070_ti_super_16gb_gddr6x_oc_edition__3_.jpg',
                'gia_goc' => 30500000,
                'gia_khuyen_mai' => 27890000,
                'thoi_gian_bao_hanh' => 36,
                'mo_ta_ngan' => 'Card màn hình hiệu năng cực khủng cho trải nghiệm gaming 2K/4K max setting, công nghệ DLSS 3.5 và dò tia Ray Tracing thời gian thực, tản nhiệt 3 quạt Axial-tech mát mẻ bền bỉ.',
                'noi_dung_chi_tiet' => '<p>Dòng card ROG Strix trang bị khung kim loại đúc, quạt Axial-tech thế hệ mới và phase nguồn mạnh mẽ tối ưu ép xung.</p>',
                'luot_xem' => 290,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 11. CPU Intel Core i7-14700K
            [
                'id' => 11,
                'danh_muc_id' => 8, // CPU
                'thuong_hieu_id' => 7,   // Intel
                'ten_san_pham' => 'CPU Intel Core i7-14700K (Up to 5.6GHz, 20 Nhân 28 Luồng)',
                'slug' => Str::slug('CPU Intel Core i7 14700K Up to 56GHz 20 Nhan 28 Luong'),
                'ma_san_pham' => 'SP-INTEL-14700K',
                'anh_chinh' => 'https://anphat.com.vn/media/product/46741_intel_core_i7_14700k.jpg',
                'gia_goc' => 11890000,
                'gia_khuyen_mai' => 10690000,
                'thoi_gian_bao_hanh' => 36,
                'mo_ta_ngan' => 'Bộ vi xử lý thế hệ thứ 14 Raptor Lake Refresh, sở hữu 8 nhân hiệu năng cao (P-core) và 12 nhân tiết kiệm điện (E-core), xung nhịp tối đa 5.6GHz, 33MB Intel Smart Cache.',
                'noi_dung_chi_tiet' => '<p>Intel Core i7-14700K cho khả năng đa nhiệm mượt mà, render đồ họa và chơi game mượt mà không nghẽn cổ chai.</p>',
                'luot_xem' => 380,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 12. Chuột Logitech G Pro X Superlight 2
            [
                'id' => 12,
                'danh_muc_id' => 4, // Gaming Gear
                'thuong_hieu_id' => 8,   // Logitech
                'ten_san_pham' => 'Chuột Gaming không dây Logitech G Pro X Superlight 2 White',
                'slug' => Str::slug('Chuot Gaming khong day Logitech G Pro X Superlight 2 White'),
                'ma_san_pham' => 'SP-LOG-GPX2-WH',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/c/h/chuot-gaming-logitech-pro-x-superlight-2-lightspeed-8.png',
                'gia_goc' => 3790000,
                'gia_khuyen_mai' => 3190000,
                'thoi_gian_bao_hanh' => 24,
                'mo_ta_ngan' => 'Chuột eSports huyền thoại với trọng lượng chỉ 60g, cảm biến HERO 2 32,000 DPI, switch lai quang học LIGHTFORCE siêu nhạy, thời lượng pin 95 giờ.',
                'noi_dung_chi_tiet' => '<p>Lựa chọn số 1 của các tuyển thủ eSports chuyên nghiệp trên toàn thế giới với độ chính xác tuyệt đối.</p>',
                'luot_xem' => 600,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 13. Bàn phím cơ AKKO 3087 RF White & Cyan
            [
                'id' => 13,
                'danh_muc_id' => 4, // Gaming Gear
                'thuong_hieu_id' => 9,   // Akko
                'ten_san_pham' => 'Bàn phím cơ AKKO 3087 RF White & Cyan (Akko Switch V3 Haze Pink)',
                'slug' => Str::slug('Ban phim co AKKO 3087 RF White Cyan Akko Switch V3 Haze Pink'),
                'ma_san_pham' => 'SP-AKKO-3087RF',
                'anh_chinh' => 'https://cdn2.cellphones.com.vn/insecure/rs:fill:0:358/q:90/plain/https://cellphones.com.vn/media/catalog/product/b/a/ban-phim-co-akko-3087-rf-one-piece-luffy-cream-4_1.jpg',
                'gia_goc' => 1690000,
                'gia_khuyen_mai' => 1290000,
                'thoi_gian_bao_hanh' => 12,
                'mo_ta_ngan' => 'Bàn phím cơ layout TKL 87 phím kết nối 2 chế độ (Dây Type-C & Wireless 2.4GHz), Keycap PBT Double-Shot bền bỉ chống bám vân tay, Switch Akko V3 mượt mà.',
                'noi_dung_chi_tiet' => '<p>AKKO 3087 RF sở hữu phối màu thanh lịch, cảm giác gõ êm ái thích hợp cho cả gõ văn bản và chơi game.</p>',
                'luot_xem' => 240,
                'noi_bat' => false,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 14. Bàn phím cơ Corsair K70 RGB PRO
            [
                'id' => 14,
                'danh_muc_id' => 4, // Gaming Gear
                'thuong_hieu_id' => 4,   // Corsair
                'ten_san_pham' => 'Bàn phím cơ Corsair K70 RGB PRO Mechanical Cherry MX Red',
                'slug' => Str::slug('Ban phim co Corsair K70 RGB PRO Mechanical Cherry MX Red'),
                'ma_san_pham' => 'SP-COR-K70PRO',
                'anh_chinh' => 'https://anphat.com.vn/media/product/41888_corsair_k70_rgb_pro__1_.jpg',
                'gia_goc' => 4290000,
                'gia_khuyen_mai' => 3590000,
                'thoi_gian_bao_hanh' => 24,
                'mo_ta_ngan' => 'Huyền thoại bàn phím cơ trang bị khung nhôm phay cao cấp, Switch Cherry MX Red siêu bền, công nghệ AXON truyền tín hiệu cực nhanh 8000Hz, dải đèn RGB per-key rực rỡ.',
                'noi_dung_chi_tiet' => '<p>Corsair K70 RGB PRO mang tiêu chuẩn thi đấu chuyên nghiệp với con lăn âm lượng bằng nhôm và núm khóa giải đấu.</p>',
                'luot_xem' => 190,
                'noi_bat' => false,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 15. Tai nghe Kingston HyperX Cloud II Red
            [
                'id' => 15,
                'danh_muc_id' => 4, // Gaming Gear
                'thuong_hieu_id' => 5,   // Kingston
                'ten_san_pham' => 'Tai nghe Gaming Kingston HyperX Cloud II 7.1 Red',
                'slug' => Str::slug('Tai nghe Gaming Kingston HyperX Cloud II 71 Red'),
                'ma_san_pham' => 'SP-HPX-CLOUD2',
                'anh_chinh' => 'https://pcmarket.vn/media/product/3534_71qv_nf8djl__sl1500_.jpg',
                'gia_goc' => 2490000,
                'gia_khuyen_mai' => 1890000,
                'thoi_gian_bao_hanh' => 24,
                'mo_ta_ngan' => 'Tai nghe gaming huyền thoại dành cho game thủ FPS, giả lập âm thanh vòm 7.1 sống động, màng loa 53mm tái tạo âm thanh chân thực, đệm tai xốp nhớ bọc da êm ái.',
                'noi_dung_chi_tiet' => '<p>HyperX Cloud II định vị chính xác từng tiếng bước chân của kẻ thù trong mọi trận đấu căng thẳng.</p>',
                'luot_xem' => 490,
                'noi_bat' => true,
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('san_pham')->insert($sanPhams);
    }
}
