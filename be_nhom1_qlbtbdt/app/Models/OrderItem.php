<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'chi_tiet_don_hang';

    protected $fillable = [
        'don_hang_id',
        'bien_the_id',
        'ten_san_pham',
        'ten_bien_the',
        'so_luong',
        'don_gia',
        'thanh_tien',
        'danh_sach_serial',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'don_gia' => 'decimal:2',
        'thanh_tien' => 'decimal:2',
        'danh_sach_serial' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'don_hang_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'bien_the_id');
    }
}
