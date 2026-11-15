<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'hinh_anh_san_pham';

    protected $fillable = [
        'san_pham_id',
        'duong_dan_anh',
        'thu_tu',
        'la_anh_chinh',
    ];

    protected $casts = [
        'thu_tu' => 'integer',
        'la_anh_chinh' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }
}
