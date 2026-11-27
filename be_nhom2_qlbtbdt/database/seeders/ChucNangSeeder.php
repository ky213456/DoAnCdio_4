<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChucNangSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table('chuc_nang')->delete();
        Schema::enableForeignKeyConstraints();

        $now = now();
        DB::table('chuc_nang')->insert([
            ['ma_chuc_nang' => 'UC09', 'ten_chuc_nang' => 'Báo cáo thống kê', 'nhom_quyen' => 'Quản trị hệ thống', 'mo_ta' => 'Xem dashboard doanh thu, KPI và báo cáo', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC10', 'ten_chuc_nang' => 'Quản lý nhân viên', 'nhom_quyen' => 'Quản trị hệ thống', 'mo_ta' => 'Thêm, sửa, xóa và khóa tài khoản nhân viên', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC11', 'ten_chuc_nang' => 'Phân quyền hệ thống', 'nhom_quyen' => 'Quản trị hệ thống', 'mo_ta' => 'Cấu hình và cấp quyền chức năng cho nhân viên', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC12', 'ten_chuc_nang' => 'Quản lý sản phẩm', 'nhom_quyen' => 'Bán hàng', 'mo_ta' => 'Thêm, sửa, xóa, đổi trạng thái sản phẩm & biến thể', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC13', 'ten_chuc_nang' => 'Quản lý Voucher', 'nhom_quyen' => 'Bán hàng', 'mo_ta' => 'Tạo, sửa, xóa, bật/tắt mã giảm giá', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC14', 'ten_chuc_nang' => 'Quản lý đơn hàng', 'nhom_quyen' => 'Bán hàng', 'mo_ta' => 'Xem, cập nhật trạng thái đơn hàng và xuất kho', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC15', 'ten_chuc_nang' => 'Quản lý khách hàng', 'nhom_quyen' => 'Bán hàng', 'mo_ta' => 'Thêm, sửa, xóa và theo dõi hồ sơ khách hàng', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC16', 'ten_chuc_nang' => 'Quản lý bình luận', 'nhom_quyen' => 'Bán hàng', 'mo_ta' => 'Kiểm duyệt, ẩn/hiện và xóa đánh giá sản phẩm', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC22', 'ten_chuc_nang' => 'Quản lý danh mục & Thương hiệu', 'nhom_quyen' => 'Kho & Bảo hành', 'mo_ta' => 'Quản lý danh mục sản phẩm và thương hiệu', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC23', 'ten_chuc_nang' => 'Quản lý nhập kho & Serial/IMEI', 'nhom_quyen' => 'Kho & Bảo hành', 'mo_ta' => 'Nhập lô hàng mới và cấp phát mã Serial/IMEI FIFO', 'created_at' => $now, 'updated_at' => $now],
            ['ma_chuc_nang' => 'UC19', 'ten_chuc_nang' => 'Tiếp nhận & xử lý bảo hành, đổi trả', 'nhom_quyen' => 'Kho & Bảo hành', 'mo_ta' => 'Tạo phiếu và cập nhật tiến độ bảo hành, đổi trả', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
