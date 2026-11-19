<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'gio_hang';

    protected $fillable = [
        'nguoi_dung_id',
        'session_id',
        'bien_the_id',
        'so_luong',
        'gia',
    ];

    protected $casts = [
        'so_luong' => 'integer',
        'gia'      => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'bien_the_id');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->variant ? (float) ($this->variant->effective_price * $this->so_luong) : 0.0;
    }
}
