<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TinNhanAiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tin_nhan_ai')->delete();

        $tinNhans = [
            // Hội thoại 1: Tư vấn Laptop Gaming & Màn hình
            [
                'id' => 1,
                'hoi_thoai_id' => 1,
                'vai_tro' => 'user',
                'noi_dung' => 'Chào bạn, mình đang có ngân sách khoảng 35 đến 40 triệu đồng. Bạn gợi ý giúp mình một mẫu laptop gaming cấu hình mạnh để vừa làm đồ họa 3D vừa chơi mượt các game AAA với ạ!',
                'san_pham_de_xuat' => null,
                'so_token' => 52,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],
            [
                'id' => 2,
                'hoi_thoai_id' => 1,
                'vai_tro' => 'assistant',
                'noi_dung' => "Chào bạn! Trong phân khúc ngân sách từ 35 - 40 triệu đồng, mẫu **Laptop ASUS ROG Strix G16 G614JV** là sự lựa chọn số 1 hiện nay với vi xử lý Intel Core i7-13650HX 14 nhân, card đồ họa rời NVIDIA RTX 4060 8GB TGP 140W cực mạnh và màn hình 165Hz chuẩn màu sắc nét.\n\nNgoài ra nếu bạn làm việc tại bàn nhiều, bạn có thể kết hợp thêm **Màn hình Gaming LG UltraGear 27GR75Q 27 inch 2K 165Hz** để có trải nghiệm hiển thị không gian rộng rãi và màu sắc chuẩn xác nhất nhé!",
                'san_pham_de_xuat' => json_encode([8, 1]), // Gợi ý SP 8 (Laptop ASUS ROG) và SP 1 (Màn hình LG)
                'so_token' => 195,
                'created_at' => now()->subDays(6),
                'updated_at' => now()->subDays(6),
            ],

            // Hội thoại 2: Tư vấn SSD & RAM
            [
                'id' => 3,
                'hoi_thoai_id' => 2,
                'vai_tro' => 'user',
                'noi_dung' => 'Mình đang muốn nâng cấp thêm 1 ổ cứng SSD tốc độ cao và 1 bộ RAM 32GB DDR5 chất lượng tốt, bạn có linh kiện nào ngon giới thiệu giúp mình nhé!',
                'san_pham_de_xuat' => null,
                'so_token' => 45,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
            [
                'id' => 4,
                'hoi_thoai_id' => 2,
                'vai_tro' => 'assistant',
                'noi_dung' => "Dạ chào bạn! Để nâng cấp dàn máy cho hiệu năng vượt trội và thẩm mỹ cao, em xin tư vấn 2 linh kiện hàng đầu sau:\n\n1. **Ổ cứng SSD Samsung 990 Pro 1TB PCIe 4.0 NVMe**: Tốc độ đọc lên tới 7,450MB/s, tản nhiệt niken kiểm soát nhiệt độ thông minh, bảo hành chính hãng đến 5 năm.\n2. **Kit RAM Corsair Vengeance RGB 32GB (2x16GB) DDR5 6000MHz**: Tốc độ bus cao 6000MHz hỗ trợ XMP 3.0/EXPO và dải đèn LED RGB 10 bóng siêu rực rỡ.\n\nCả 2 sản phẩm này đều đang có sẵn hàng và được áp dụng mã giảm giá khi đặt mua trực tiếp trên website ạ!",
                'san_pham_de_xuat' => json_encode([4, 6]), // Gợi ý SP 4 (SSD Samsung 990 Pro) và SP 6 (RAM Corsair)
                'so_token' => 210,
                'created_at' => now()->subDays(4),
                'updated_at' => now()->subDays(4),
            ],
        ];

        DB::table('tin_nhan_ai')->insert($tinNhans);
    }
}
