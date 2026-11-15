<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThuongHieuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('thuong_hieu')->truncate();

        $thuongHieus = [
            [
                'id' => 1,
                'ten_thuong_hieu' => 'ASUS',
                'slug' => Str::slug('ASUS'),
                'logo' => 'https://images.seeklogo.com/logo-png/17/2/asus-republic-of-gamers-logo-png_seeklogo-175206.png',
                'mo_ta' => 'Thương hiệu máy tính, laptop và linh kiện hàng đầu Đài Loan.',
                'xuat_xu' => 'Đài Loan',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'ten_thuong_hieu' => 'Samsung',
                'slug' => Str::slug('Samsung'),
                'logo' => 'https://images.seeklogo.com/logo-png/12/2/samsung-logo-png_seeklogo-122023.png',
                'mo_ta' => 'Tập đoàn công nghệ đa quốc gia Hàn Quốc hàng đầu về bộ nhớ và màn hình.',
                'xuat_xu' => 'Hàn Quốc',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'ten_thuong_hieu' => 'LG',
                'slug' => Str::slug('LG'),
                'logo' => 'https://images.seeklogo.com/logo-png/8/2/lg-electronics-logo-png_seeklogo-83711.png',
                'mo_ta' => 'Thương hiệu màn hình và thiết bị điện tử nổi tiếng toàn cầu.',
                'xuat_xu' => 'Hàn Quốc',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'ten_thuong_hieu' => 'Corsair',
                'slug' => Str::slug('Corsair'),
                'logo' => 'https://images.seeklogo.com/logo-png/26/2/corsair-logo-png_seeklogo-268746.png',
                'mo_ta' => 'Thương hiệu phần cứng cao cấp, RAM và thiết bị chơi game Hoa Kỳ.',
                'xuat_xu' => 'Mỹ',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'ten_thuong_hieu' => 'Kingston',
                'slug' => Str::slug('Kingston'),
                'logo' => 'https://logos-world.net/wp-content/uploads/2020/12/Kingston-Logo-History-700x280.jpg',
                'mo_ta' => 'Nhà sản xuất bộ nhớ RAM, SSD và tai nghe gaming nổi tiếng thế giới.',
                'xuat_xu' => 'Mỹ',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 6,
                'ten_thuong_hieu' => 'Dell',
                'slug' => Str::slug('Dell'),
                'logo' => 'https://images.seeklogo.com/logo-png/31/2/dell-logo-png_seeklogo-318421.png',
                'mo_ta' => 'Hãng sản xuất máy tính xách tay và màn hình đồ họa chuyên nghiệp hàng đầu.',
                'xuat_xu' => 'Mỹ',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 7,
                'ten_thuong_hieu' => 'Intel',
                'slug' => Str::slug('Intel'),
                'logo' => 'https://images.seeklogo.com/logo-png/22/2/intel-logo-png_seeklogo-226413.png',
                'mo_ta' => 'Tập đoàn vi xử lý máy tính lớn nhất thế giới.',
                'xuat_xu' => 'Mỹ',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 8,
                'ten_thuong_hieu' => 'Logitech',
                'slug' => Str::slug('Logitech'),
                'logo' => 'https://images.seeklogo.com/logo-png/29/2/logitech-gaming-logo-png_seeklogo-298518.png',
                'mo_ta' => 'Thương hiệu phụ kiện máy tính và Gaming Gear Thụy Sĩ.',
                'xuat_xu' => 'Thụy Sĩ',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 9,
                'ten_thuong_hieu' => 'Akko',
                'slug' => Str::slug('Akko'),
                'logo' => 'https://akkogear.com.vn/wp-content/uploads/2026/01/logo-akko-moi.png',
                'mo_ta' => 'Thương hiệu bàn phím cơ thời trang và phụ kiện switch cao cấp.',
                'xuat_xu' => 'Trung Quốc',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 10,
                'ten_thuong_hieu' => 'G.Skill',
                'slug' => Str::slug('G.Skill'),
                'logo' => 'https://images.seeklogo.com/logo-png/6/1/gskill-logo-png_seeklogo-63849.png',
                'mo_ta' => 'Thương hiệu RAM máy tính hiệu năng cao dành cho ép xung và game thủ.',
                'xuat_xu' => 'Đài Loan',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('thuong_hieu')->insert($thuongHieus);
    }
}
