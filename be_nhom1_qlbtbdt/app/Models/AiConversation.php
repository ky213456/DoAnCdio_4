<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiConversation extends Model
{
    use HasFactory;

    protected $table = 'hoi_thoai_ai';

    protected $fillable = [
        'nguoi_dung_id',
        'session_id',
        'tieu_de',
        'ngu_canh',
        'trang_thai',
    ];

    protected $casts = [
        'trang_thai' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function messages()
    {
        return $this->hasMany(AiMessage::class, 'hoi_thoai_id');
    }
}
