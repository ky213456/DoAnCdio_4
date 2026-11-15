<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'bien_the_san_pham';

    protected $fillable = [
        'san_pham_id',
        'sku',
        'ten_bien_the',
        'mau_sac',
        'ram',
        'o_cung',
        'gia_ban',
        'gia_khuyen_mai',
        'so_luong_ton',
        'hinh_anh',
        'trang_thai',
    ];

    protected $casts = [
        'gia_ban' => 'decimal:2',
        'gia_khuyen_mai' => 'decimal:2',
        'so_luong_ton' => 'integer',
        'trang_thai' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class, 'bien_the_id');
    }

    public function scopeInStock($query)
    {
        return $query->where('so_luong_ton', '>', 0)->where('trang_thai', true);
    }

    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->gia_khuyen_mai ?? $this->gia_ban);
    }
}
