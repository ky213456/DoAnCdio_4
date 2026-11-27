<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('phieu_bao_hanh')) {
            Schema::create('phieu_bao_hanh', function (Blueprint $table) {
                $table->id();
                $table->string('ma_phieu')->unique();
                $table->unsignedBigInteger('serial_id')->nullable();
                $table->string('so_serial')->nullable();
                $table->string('ten_khach_hang');
                $table->string('so_dien_thoai', 20)->nullable();
                $table->string('ten_thiet_bi');
                $table->enum('loai_yeu_cau', ['bao_hanh', 'doi_tra'])->default('bao_hanh');
                $table->text('mo_ta_loi');
                $table->enum('trang_thai', ['tiep_nhan', 'dang_kiem_tra', 'cho_linh_kien', 'da_sua_xong', 'da_tra_hang'])->default('tiep_nhan');
                $table->string('ky_thuat_vien')->nullable();
                $table->date('han_tra')->nullable();
                $table->decimal('chi_phi_du_kien', 15, 2)->default(0);
                $table->text('ghi_chu')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('nguoi_dung', 'quyen_han')) {
            Schema::table('nguoi_dung', function (Blueprint $table) {
                $table->json('quyen_han')->nullable()->after('vai_tro');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('phieu_bao_hanh');
        if (Schema::hasColumn('nguoi_dung', 'quyen_han')) {
            Schema::table('nguoi_dung', function (Blueprint $table) {
                $table->dropColumn('quyen_han');
            });
        }
    }
};
