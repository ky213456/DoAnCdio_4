<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serial_san_pham', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_the_id')->constrained('bien_the_san_pham')->onDelete('cascade');
            $table->foreignId('don_hang_id')->nullable()->constrained('don_hang')->onDelete('set null');
            $table->string('so_serial', 100)->unique();
            $table->string('imei', 100)->nullable();
            $table->enum('trang_thai', ['trong_kho', 'giu_cho', 'da_ban', 'loi'])->default('trong_kho');
            $table->timestamp('ngay_nhap_kho')->useCurrent();
            $table->timestamp('ngay_xuat_kho')->nullable();
            $table->date('han_bao_hanh')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serial_san_pham');
    }
};
