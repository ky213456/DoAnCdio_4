<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('chuc_nang')) {
            Schema::create('chuc_nang', function (Blueprint $table) {
                $table->id();
                $table->string('ma_chuc_nang', 50)->unique();
                $table->string('ten_chuc_nang', 150);
                $table->string('nhom_quyen', 100)->nullable();
                $table->string('mo_ta', 255)->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('phan_quyen')) {
            Schema::create('phan_quyen', function (Blueprint $table) {
                $table->id();
                $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
                $table->foreignId('chuc_nang_id')->constrained('chuc_nang')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['nguoi_dung_id', 'chuc_nang_id']);
            });
        }

        if (Schema::hasColumn('nguoi_dung', 'quyen_han')) {
            Schema::table('nguoi_dung', function (Blueprint $table) {
                $table->dropColumn('quyen_han');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('phan_quyen');
        Schema::dropIfExists('chuc_nang');
    }
};
