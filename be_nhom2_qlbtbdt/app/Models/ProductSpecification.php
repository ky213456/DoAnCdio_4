<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSpecification extends Model
{
    use HasFactory;

    protected $table = 'thong_so_ky_thuat';

    protected $fillable = [
        'san_pham_id',
        'cpu',
        'ram',
        'o_cung',
        'card_do_hoa',
        'man_hinh',
        'do_phan_giai',
        'tan_so_quet',
        'pin',
        'trong_luong',
        'cong_ket_noi',
        'he_dieu_hanh',
        'thong_so_them',
    ];

    protected $casts = [
        'thong_so_them' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'san_pham_id');
    }
}
