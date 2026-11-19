<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('gio_hang', 'gia')) {
            Schema::table('gio_hang', function (Blueprint $table) {
                $table->decimal('gia', 15, 2)->nullable()->after('so_luong');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('gio_hang', 'gia')) {
            Schema::table('gio_hang', function (Blueprint $table) {
                $table->dropColumn('gia');
            });
        }
    }
};
