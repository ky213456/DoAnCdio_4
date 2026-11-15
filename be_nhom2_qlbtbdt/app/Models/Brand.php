<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = 'thuong_hieu';

    protected $fillable = [
        'ten_thuong_hieu',
        'slug',
        'logo',
        'mo_ta',
        'xuat_xu',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
    ];

    public function products()
    {
        return $this->hasMany(Product::class, 'thuong_hieu_id');
    }

    public function scopeActive($query)
    {
        return $query->where('trang_thai', true);
    }
}
