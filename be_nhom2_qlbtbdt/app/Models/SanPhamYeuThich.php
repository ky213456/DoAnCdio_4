<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPhamYeuThich extends Model
{
    use HasFactory;

    protected $table = 'san_pham_yeu_thich';

    protected $fillable = [
        'nguoi_dung_id',
        'san_pham_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function product()
    {
        return $this->belongsTo(SanPham::class, 'san_pham_id');
    }
}
