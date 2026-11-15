<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'ma_giam_gia';

    protected $fillable = [
        'ma_code',
        'tieu_de',
        'loai_giam_gia',
        'gia_tri_giam',
        'giam_toi_da',
        'don_hang_toi_thieu',
        'so_luong',
        'da_su_dung',
        'ngay_bat_dau',
        'ngay_ket_thuc',
        'trang_thai',
    ];

    protected $casts = [
        'gia_tri_giam' => 'decimal:2',
        'giam_toi_da' => 'decimal:2',
        'don_hang_toi_thieu' => 'decimal:2',
        'so_luong' => 'integer',
        'da_su_dung' => 'integer',
        'ngay_bat_dau' => 'datetime',
        'ngay_ket_thuc' => 'datetime',
        'trang_thai' => 'boolean',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'ma_giam_gia_id');
    }

    public function isValidForOrder(float $orderTotal): bool
    {
        if (!$this->trang_thai) return false;
        if ($this->so_luong > 0 && $this->da_su_dung >= $this->so_luong) return false;
        if ($this->ngay_bat_dau && now()->lt($this->ngay_bat_dau)) return false;
        if ($this->ngay_ket_thuc && now()->gt($this->ngay_ket_thuc)) return false;
        if ($orderTotal < $this->don_hang_toi_thieu) return false;
        return true;
    }

    public function calculateDiscount(float $orderTotal): float
    {
        if (!$this->isValidForOrder($orderTotal)) return 0;
        
        if ($this->loai_giam_gia === 'percent') {
            $discount = ($orderTotal * $this->gia_tri_giam) / 100;
            if ($this->giam_toi_da && $discount > $this->giam_toi_da) {
                return (float) $this->giam_toi_da;
            }
            return (float) $discount;
        }

        return (float) min($this->gia_tri_giam, $orderTotal);
    }
}
