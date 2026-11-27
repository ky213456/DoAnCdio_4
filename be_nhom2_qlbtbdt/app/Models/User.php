<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ho_ten',
        'email',
        'password',
        'so_dien_thoai',
        'vai_tro',
        'anh_dai_dien',
        'dia_chi',
        'trang_thai',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'trang_thai' => 'boolean',
        ];
    }

    // Role check helpers
    public function isAdmin(): bool
    {
        return $this->vai_tro === 'admin';
    }

    public function isStaff(): bool
    {
        return in_array($this->vai_tro, ['admin', 'staff']);
    }

    public function isCustomer(): bool
    {
        return $this->vai_tro === 'customer';
    }

    public function chucNangs()
    {
        return $this->belongsToMany(ChucNang::class, 'phan_quyen', 'nguoi_dung_id', 'chuc_nang_id')
            ->withTimestamps();
    }

    public function getPermissionCodes(): array
    {
        if ($this->isAdmin()) {
            return ChucNang::pluck('ma_chuc_nang')->toArray();
        }
        return $this->chucNangs()->pluck('ma_chuc_nang')->toArray();
    }

    public function hasPermission(string $maChucNang): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        return $this->chucNangs()->where('ma_chuc_nang', $maChucNang)->exists();
    }

    // Relationships
    public function addresses()
    {
        return $this->hasMany(UserAddress::class, 'nguoi_dung_id');
    }

    public function favorites()
    {
        return $this->hasMany(SanPhamYeuThich::class, 'nguoi_dung_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'nguoi_dung_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'nguoi_dung_id');
    }

    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'nguoi_dung_id');
    }

    public function aiConversations()
    {
        return $this->hasMany(AiConversation::class, 'nguoi_dung_id');
    }

    // Query Scope
    public function scopeActive($query)
    {
        return $query->where('trang_thai', true);
    }
}
