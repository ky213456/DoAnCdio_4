<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'don_hang';

    protected $fillable = [
        'ma_don_hang',
        'nguoi_dung_id',
        'ma_giam_gia_id',
        'ho_ten_nguoi_nhan',
        'so_dien_thoai_nhan',
        'dia_chi_giao_hang',
        'ghi_chu',
        'tong_tien_hang',
        'so_tien_giam',
        'phi_van_chuyen',
        'tong_thanh_toan',
        'phuong_thuc_thanh_toan',
        'trang_thai_thanh_toan',
        'trang_thai_don_hang',
        'ngay_dat',
    ];

    protected $casts = [
        'tong_tien_hang' => 'decimal:2',
        'so_tien_giam' => 'decimal:2',
        'phi_van_chuyen' => 'decimal:2',
        'tong_thanh_toan' => 'decimal:2',
        'ngay_dat' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'nguoi_dung_id');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'ma_giam_gia_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'don_hang_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'don_hang_id');
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class, 'don_hang_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'don_hang_id');
    }
}
