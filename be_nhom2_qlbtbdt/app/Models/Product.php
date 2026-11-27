<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'san_pham';

    protected $fillable = [
        'danh_muc_id',
        'thuong_hieu_id',
        'ten_san_pham',
        'slug',
        'ma_san_pham',
        'anh_chinh',
        'gia_goc',
        'gia_khuyen_mai',
        'thoi_gian_bao_hanh',
        'mo_ta_ngan',
        'noi_dung_chi_tiet',
        'luot_xem',
        'noi_bat',
        'trang_thai',
    ];

    protected $casts = [
        'gia_goc' => 'decimal:2',
        'gia_khuyen_mai' => 'decimal:2',
        'thoi_gian_bao_hanh' => 'integer',
        'luot_xem' => 'integer',
        'noi_bat' => 'boolean',
        'trang_thai' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'danh_muc_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'thuong_hieu_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'san_pham_id')->orderBy('thu_tu');
    }

    public function specification()
    {
        return $this->hasOne(ProductSpecification::class, 'san_pham_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'san_pham_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'san_pham_id');
    }

    public function favorites()
    {
        return $this->hasMany(SanPhamYeuThich::class, 'san_pham_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('trang_thai', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('noi_bat', true);
    }

    // Accessors
    public function getAverageRatingAttribute(): float
    {
        return (float) ($this->reviews()->where('trang_thai_duyet', true)->avg('so_sao') ?? 5.0);
    }
}
