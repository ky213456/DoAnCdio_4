<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('danh_gia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('san_pham_id')->constrained('san_pham')->onDelete('cascade');
            $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
            $table->foreignId('don_hang_id')->nullable()->constrained('don_hang')->onDelete('set null');
            $table->tinyInteger('so_sao')->default(5); // 1-5 sao
            $table->text('noi_dung')->nullable();
            $table->json('hinh_anh_dinh_kem')->nullable();
            $table->boolean('da_mua_hang')->default(true);
            $table->boolean('trang_thai_duyet')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('danh_gia');
    }
};
