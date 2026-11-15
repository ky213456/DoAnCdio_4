<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chi_tiet_don_hang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_hang_id')->constrained('don_hang')->onDelete('cascade');
            $table->foreignId('bien_the_id')->nullable()->constrained('bien_the_san_pham')->onDelete('set null');
            $table->string('ten_san_pham');
            $table->string('ten_bien_the')->nullable();
            $table->integer('so_luong')->default(1);
            $table->decimal('don_gia', 15, 2);
            $table->decimal('thanh_tien', 15, 2);
            $table->json('danh_sach_serial')->nullable(); // Lưu mã serial/IMEI đã gán cho đơn
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chi_tiet_don_hang');
    }
};
