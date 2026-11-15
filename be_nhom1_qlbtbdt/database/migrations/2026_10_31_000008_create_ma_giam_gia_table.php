<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ma_giam_gia', function (Blueprint $table) {
            $table->id();
            $table->string('ma_code', 50)->unique();
            $table->string('tieu_de');
            $table->enum('loai_giam_gia', ['percent', 'fixed'])->default('percent');
            $table->decimal('gia_tri_giam', 15, 2);
            $table->decimal('giam_toi_da', 15, 2)->nullable();
            $table->decimal('don_hang_toi_thieu', 15, 2)->default(0);
            $table->integer('so_luong')->default(0);
            $table->integer('da_su_dung')->default(0);
            $table->timestamp('ngay_bat_dau')->nullable();
            $table->timestamp('ngay_ket_thuc')->nullable();
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ma_giam_gia');
    }
};
