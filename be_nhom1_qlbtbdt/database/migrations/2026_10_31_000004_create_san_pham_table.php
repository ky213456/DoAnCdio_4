<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('danh_muc_id')->constrained('danh_muc')->onDelete('cascade');
            $table->foreignId('thuong_hieu_id')->constrained('thuong_hieu')->onDelete('cascade');
            $table->string('ten_san_pham');
            $table->string('slug')->unique();
            $table->string('ma_san_pham', 50)->unique();
            $table->string('anh_chinh')->nullable();
            $table->decimal('gia_goc', 15, 2);
            $table->decimal('gia_khuyen_mai', 15, 2)->nullable();
            $table->integer('thoi_gian_bao_hanh')->default(12); // Số tháng bảo hành
            $table->text('mo_ta_ngan')->nullable();
            $table->longText('noi_dung_chi_tiet')->nullable();
            $table->integer('luot_xem')->default(0);
            $table->boolean('noi_bat')->default(false);
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('san_pham');
    }
};
