<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuBaoHanh extends Model
{
    use HasFactory;

    protected $table = 'phieu_bao_hanh';

    protected $fillable = [
        'ma_phieu',
        'serial_id',
        'so_serial',
        'ten_khach_hang',
        'so_dien_thoai',
        'ten_thiet_bi',
        'loai_yeu_cau',
        'mo_ta_loi',
        'trang_thai',
        'ky_thuat_vien',
        'han_tra',
        'chi_phi_du_kien',
        'ghi_chu',
    ];

    protected $casts = [
        'han_tra' => 'date',
        'chi_phi_du_kien' => 'decimal:2',
    ];
}
