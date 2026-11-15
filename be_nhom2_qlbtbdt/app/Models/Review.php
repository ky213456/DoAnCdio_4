<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = 'danh_gia';

    protected $fillable = [
        'san_pham_id',
        'nguoi_dung_id',
        'don_hang_id',
        'so_sao',
        'noi_dung',
        'hinh_anh_dinh_kem',
        'da_mua_hang',
        'trang_thai_duyet',
    ];

    protected $casts = [
        'so_sao' => 'integer',
        'hinh_anh_dinh_kem' => 'array',
        'da_mua_hang' => 'boolean',
        'trang_thai_duyet' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'don_hang_id');
    }
}
