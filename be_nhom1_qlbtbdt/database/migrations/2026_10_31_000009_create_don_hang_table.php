<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('don_hang', function (Blueprint $table) {
            $table->id();
            $table->string('ma_don_hang', 50)->unique();
            $table->foreignId('nguoi_dung_id')->nullable()->constrained('nguoi_dung')->onDelete('set null');
            $table->foreignId('ma_giam_gia_id')->nullable()->constrained('ma_giam_gia')->onDelete('set null');
            
            // Thông tin giao nhận
            $table->string('ho_ten_nguoi_nhan');
            $table->string('so_dien_thoai_nhan', 20);
            $table->string('dia_chi_giao_hang');
            $table->text('ghi_chu')->nullable();
            
            // Tài chính đơn hàng
            $table->decimal('tong_tien_hang', 15, 2);
            $table->decimal('so_tien_giam', 15, 2)->default(0);
            $table->decimal('phi_van_chuyen', 15, 2)->default(0);
            $table->decimal('tong_thanh_toan', 15, 2);
            
            // Phương thức & trạng thái
            $table->enum('phuong_thuc_thanh_toan', ['cod', 'vnpay', 'payos'])->default('cod');
            $table->enum('trang_thai_thanh_toan', ['chua_thanh_toan', 'da_thanh_toan', 'that_bai', 'hoan_tien'])->default('chua_thanh_toan');
            $table->enum('trang_thai_don_hang', [
                'cho_xac_nhan',
                'da_xac_nhan',
                'dang_dong_goi',
                'dang_giao',
                'giao_thanh_cong',
                'da_huy'
            ])->default('cho_xac_nhan');
            
            $table->timestamp('ngay_dat')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('don_hang');
    }
};
