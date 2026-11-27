<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tắt ràng buộc khóa ngoại để truncate và nạp dữ liệu an toàn
        Schema::disableForeignKeyConstraints();

        $this->call([
            // 1. Nhóm Người dùng, Chức năng & Phân quyền
            NguoiDungSeeder::class,
            ChucNangSeeder::class,
            PhanQuyenSeeder::class,
            DiaChiNguoiDungSeeder::class,

            // 2. Nhóm Phân loại & Thương hiệu
            ThuongHieuSeeder::class,
            DanhMucSeeder::class,

            // 3. Nhóm Sản phẩm & Thông tin kỹ thuật
            SanPhamSeeder::class,
            HinhAnhSanPhamSeeder::class,
            ThongSoKyThuatSeeder::class,
            BienTheSanPhamSeeder::class,

            // 4. Nhóm Khuyến mãi & Bán hàng
            MaGiamGiaSeeder::class,
            DonHangSeeder::class,
            ChiTietDonHangSeeder::class,
            SerialSanPhamSeeder::class,
            ThanhToanSeeder::class,

            // 5. Nhóm Giỏ hàng & Đánh giá
            GioHangSeeder::class,
            DanhGiaSeeder::class,

            // 6. Nhóm Trợ lý AI
            HoiThoaiAiSeeder::class,
            TinNhanAiSeeder::class,
        ]);

        // Bật lại ràng buộc khóa ngoại
        Schema::enableForeignKeyConstraints();
    }
}
