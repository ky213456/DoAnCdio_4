<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiMessage extends Model
{
    use HasFactory;

    protected $table = 'tin_nhan_ai';

    protected $fillable = [
        'hoi_thoai_id',
        'vai_tro',
        'noi_dung',
        'san_pham_de_xuat',
        'so_token',
    ];

    protected $casts = [
        'san_pham_de_xuat' => 'array',
        'so_token' => 'integer',
    ];

    public function conversation()
    {
        return $this->belongsTo(AiConversation::class, 'hoi_thoai_id');
    }
}
