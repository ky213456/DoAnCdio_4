<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'dia_chi_nguoi_dung';

    protected $fillable = [
        'nguoi_dung_id',
        'ho_ten_nguoi_nhan',
        'so_dien_thoai',
        'dia_chi_chi_tiet',
        'phuong_xa',
        'quan_huyen',
        'tinh_thanh',
        'mac_dinh',
    ];

    protected $casts = [
        'mac_dinh' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }
}
