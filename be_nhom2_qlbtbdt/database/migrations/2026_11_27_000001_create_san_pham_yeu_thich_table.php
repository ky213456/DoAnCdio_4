<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('san_pham_yeu_thich')) {
            Schema::create('san_pham_yeu_thich', function (Blueprint $table) {
                $table->id();
                $table->foreignId('nguoi_dung_id')->constrained('nguoi_dung')->onDelete('cascade');
                $table->foreignId('san_pham_id')->constrained('san_pham')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['nguoi_dung_id', 'san_pham_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('san_pham_yeu_thich');
    }
};
