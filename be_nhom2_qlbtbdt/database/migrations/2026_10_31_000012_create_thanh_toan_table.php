<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanh_toan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('don_hang_id')->constrained('don_hang')->onDelete('cascade');
            $table->string('ma_giao_dich', 100)->unique();
            $table->string('cong_thanh_toan', 50); // vnpay, payos
            $table->decimal('so_tien', 15, 2);
            $table->string('loai_tien', 10)->default('VND');
            $table->enum('trang_thai', ['pending', 'success', 'failed', 'refunded'])->default('pending');
            $table->string('ma_phan_hoi_cong')->nullable();
            $table->json('du_lieu_phan_hoi')->nullable();
            $table->timestamp('thoi_gian_thanh_toan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanh_toan');
    }
};
