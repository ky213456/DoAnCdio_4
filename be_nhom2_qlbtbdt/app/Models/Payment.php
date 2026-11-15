<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'thanh_toan';

    protected $fillable = [
        'don_hang_id',
        'ma_giao_dich',
        'cong_thanh_toan',
        'so_tien',
        'loai_tien',
        'trang_thai',
        'ma_phan_hoi_cong',
        'du_lieu_phan_hoi',
        'thoi_gian_thanh_toan',
    ];

    protected $casts = [
        'so_tien' => 'decimal:2',
        'du_lieu_phan_hoi' => 'array',
        'thoi_gian_thanh_toan' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'don_hang_id');
    }
}
