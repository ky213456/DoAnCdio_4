<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thong_so_ky_thuat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('san_pham_id')->unique()->constrained('san_pham')->onDelete('cascade');
            $table->string('cpu')->nullable();
            $table->string('ram')->nullable();
            $table->string('o_cung')->nullable();
            $table->string('card_do_hoa')->nullable();
            $table->string('man_hinh')->nullable();
            $table->string('do_phan_giai')->nullable();
            $table->string('tan_so_quet')->nullable();
            $table->string('pin')->nullable();
            $table->string('trong_luong')->nullable();
            $table->text('cong_ket_noi')->nullable();
            $table->string('he_dieu_hanh')->nullable();
            $table->json('thong_so_them')->nullable(); // Lưu các thông số đặc biệt khác dạng JSON
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thong_so_ky_thuat');
    }
};
