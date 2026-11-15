<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bien_the_san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('san_pham_id')->constrained('san_pham')->onDelete('cascade');
            $table->string('sku', 50)->unique();
            $table->string('ten_bien_the');
            $table->string('mau_sac')->nullable();
            $table->string('ram')->nullable();
            $table->string('o_cung')->nullable();
            $table->decimal('gia_ban', 15, 2);
            $table->decimal('gia_khuyen_mai', 15, 2)->nullable();
            $table->integer('so_luong_ton')->default(0);
            $table->string('hinh_anh')->nullable();
            $table->boolean('trang_thai')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bien_the_san_pham');
    }
};
