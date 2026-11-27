<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChucNang extends Model
{
    protected $table = 'chuc_nang';

    protected $fillable = [
        'ma_chuc_nang',
        'ten_chuc_nang',
        'nhom_quyen',
        'mo_ta',
    ];

    public function nguoiDungs()
    {
        return $this->belongsToMany(User::class, 'phan_quyen', 'chuc_nang_id', 'nguoi_dung_id')
            ->withTimestamps();
    }
}
