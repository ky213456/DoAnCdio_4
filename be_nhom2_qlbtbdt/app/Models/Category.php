<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'danh_muc';

    protected $fillable = [
        'ten_danh_muc',
        'slug',
        'mo_ta',
        'hinh_anh',
        'danh_muc_cha_id',
        'thu_tu',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
        'thu_tu' => 'integer',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'danh_muc_cha_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'danh_muc_cha_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'danh_muc_id');
    }

    public function scopeActive($query)
    {
        return $query->where('trang_thai', true);
    }
}
