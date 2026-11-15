<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    use HasFactory;

    protected $table = 'serial_san_pham';

    protected $fillable = [
        'bien_the_id',
        'don_hang_id',
        'so_serial',
        'imei',
        'trang_thai',
        'ngay_nhap_kho',
        'ngay_xuat_kho',
        'han_bao_hanh',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_nhap_kho' => 'datetime',
        'ngay_xuat_kho' => 'datetime',
        'han_bao_hanh' => 'date',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'bien_the_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'don_hang_id');
    }

    public function scopeInStock($query)
    {
        return $query->where('trang_thai', 'trong_kho');
    }
}
