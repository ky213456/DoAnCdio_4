<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class NguoiDungSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('nguoi_dung')->delete();
        Schema::enableForeignKeyConstraints();

        DB::table('nguoi_dung')->insert([
            // 1. Quản trị viên (Admin)
            [
                'id' => 1,
                'ho_ten' => 'Nguyễn Văn Kỳ',
                'email' => 'admin@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'so_dien_thoai' => '0905123456',
                'vai_tro' => 'admin',
                'anh_dai_dien' => 'https://ui-avatars.com/api/?name=Admin&background=0D8ABC&color=fff',
                'dia_chi' => 'Đà Nẵng, Việt Nam',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 2. Nhân viên chung (Staff - mặc định Bán hàng)
            [
                'id' => 2,
                'ho_ten' => 'Lê Văn C',
                'email' => 'staff@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'so_dien_thoai' => '0905654321',
                'vai_tro' => 'staff',
                'anh_dai_dien' => 'https://ui-avatars.com/api/?name=Staff&background=28a745&color=fff',
                'dia_chi' => 'Đà Nẵng, Việt Nam',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 3. Khách hàng 1 (Customer A)
            [
                'id' => 3,
                'ho_ten' => 'Nguyễn Văn A',
                'email' => 'user@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'so_dien_thoai' => '0912345678',
                'vai_tro' => 'customer',
                'anh_dai_dien' => 'https://ui-avatars.com/api/?name=Nguyen+Van+A&background=ffc107&color=333',
                'dia_chi' => '123 Đường Hải Phòng, Hải Châu, Đà Nẵng',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 4. Khách hàng 2 (Customer B)
            [
                'id' => 4,
                'ho_ten' => 'Trần Thị B',
                'email' => 'customer2@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'so_dien_thoai' => '0987654321',
                'vai_tro' => 'customer',
                'anh_dai_dien' => 'https://ui-avatars.com/api/?name=Tran+Thi+B&background=e83e8c&color=fff',
                'dia_chi' => '456 Đường Nguyễn Lương Bằng, Liên Chiểu, Đà Nẵng',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 5. Khách hàng 3 (Customer C)
            [
                'id' => 5,
                'ho_ten' => 'Lê Hoàng Nam',
                'email' => 'customer3@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'so_dien_thoai' => '0935123987',
                'vai_tro' => 'customer',
                'anh_dai_dien' => 'https://ui-avatars.com/api/?name=Le+Hoang+Nam&background=6f42c1&color=fff',
                'dia_chi' => '78 Phố Huế, Hoàn Kiếm, Hà Nội',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 6. Nhân viên bán hàng
            [
                'id' => 6,
                'ho_ten' => 'Trần Minh Tuấn (NV Bán hàng)',
                'email' => 'banhang@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'so_dien_thoai' => '0905222333',
                'vai_tro' => 'staff',
                'anh_dai_dien' => 'https://ui-avatars.com/api/?name=Ban+Hang&background=0d6efd&color=fff',
                'dia_chi' => 'Đà Nẵng, Việt Nam',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 7. Nhân viên kho & bảo hành
            [
                'id' => 7,
                'ho_ten' => 'Phạm Quốc Bảo (NV Kho & BH)',
                'email' => 'khobaohanh@gmail.com',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
                'so_dien_thoai' => '0905444555',
                'vai_tro' => 'staff',
                'anh_dai_dien' => 'https://ui-avatars.com/api/?name=Kho+BH&background=fd7e14&color=fff',
                'dia_chi' => 'Đà Nẵng, Việt Nam',
                'trang_thai' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
