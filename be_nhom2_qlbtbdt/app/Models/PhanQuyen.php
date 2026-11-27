<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhanQuyen extends Model
{
    protected $table = 'phan_quyen';

    protected $fillable = [
        'nguoi_dung_id',
        'chuc_nang_id',
    ];

    public function nguoiDung()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function chucNang()
    {
        return $this->belongsTo(ChucNang::class, 'chuc_nang_id');
    }
}
