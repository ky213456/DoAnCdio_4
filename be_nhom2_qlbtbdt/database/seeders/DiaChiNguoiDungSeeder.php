<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DiaChiNguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dia_chi_nguoi_dung')->delete();

        $diaChis = [
            // Địa chỉ 1: Khách hàng Nguyễn Văn A (nguoi_dung_id = 3) - Mặc định
            [
                'id' => 1,
                'nguoi_dung_id' => 3,
                'ho_ten_nguoi_nhan' => 'Nguyễn Văn A',
                'so_dien_thoai' => '0912345678',
                'dia_chi_chi_tiet' => '123 Đường Hải Phòng',
                'phuong_xa' => 'Phường Thạch Thang',
                'quan_huyen' => 'Quận Hải Châu',
                'tinh_thanh' => 'Thành phố Đà Nẵng',
                'mac_dinh' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Địa chỉ 2: Khách hàng Nguyễn Văn A (nguoi_dung_id = 3) - Nơi làm việc
            [
                'id' => 2,
                'nguoi_dung_id' => 3,
                'ho_ten_nguoi_nhan' => 'Nguyễn Văn A (Văn phòng)',
                'so_dien_thoai' => '0912345678',
                'dia_chi_chi_tiet' => '45 Đường Quang Trung',
                'phuong_xa' => 'Phường Hải Châu 1',
                'quan_huyen' => 'Quận Hải Châu',
                'tinh_thanh' => 'Thành phố Đà Nẵng',
                'mac_dinh' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Địa chỉ 3: Khách hàng Trần Thị B (nguoi_dung_id = 4) - Mặc định
            [
                'id' => 3,
                'nguoi_dung_id' => 4,
                'ho_ten_nguoi_nhan' => 'Trần Thị B',
                'so_dien_thoai' => '0987654321',
                'dia_chi_chi_tiet' => '456 Đường Nguyễn Lương Bằng',
                'phuong_xa' => 'Phường Hòa Khánh Bắc',
                'quan_huyen' => 'Quận Liên Chiểu',
                'tinh_thanh' => 'Thành phố Đà Nẵng',
                'mac_dinh' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Địa chỉ 4: Khách hàng Lê Hoàng Nam (nguoi_dung_id = 5) - Mặc định
            [
                'id' => 4,
                'nguoi_dung_id' => 5,
                'ho_ten_nguoi_nhan' => 'Lê Hoàng Nam',
                'so_dien_thoai' => '0935123987',
                'dia_chi_chi_tiet' => '78 Phố Huế',
                'phuong_xa' => 'Phường Hàng Bài',
                'quan_huyen' => 'Quận Hoàn Kiếm',
                'tinh_thanh' => 'Thành phố Hà Nội',
                'mac_dinh' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('dia_chi_nguoi_dung')->insert($diaChis);
    }
}
