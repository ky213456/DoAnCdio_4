<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tin_nhan_ai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoi_thoai_id')->constrained('hoi_thoai_ai')->onDelete('cascade');
            $table->enum('vai_tro', ['user', 'assistant', 'system'])->default('user');
            $table->longText('noi_dung');
            $table->json('san_pham_de_xuat')->nullable(); // Mảng ID sản phẩm hoặc thông tin cấu hình AI gợi ý
            $table->integer('so_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tin_nhan_ai');
    }
};
