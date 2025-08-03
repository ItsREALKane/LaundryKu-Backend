<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Owner extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'owners';
    
    protected $fillable = [
        'username',
        'email',
        'password',
        'nama_laundry',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    /**
     * Get the pesanan for the owner.
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_owner');
    }

    /**
     * Get the tagihan for the owner.
     */
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_owner');
    }

    /**
     * Get the detail tagihan for the owner.
     */
    public function detailTagihan()
    {
        return $this->hasMany(DetailTagihan::class, 'id_owner');
    }
    
    /**
     * Get the pengeluaran for the owner.
     */
    public function pengeluaran()
    {
        return $this->hasMany(Pengeluaran::class, 'id_owner');
    }

    /**
     * Get the layanan for the owner.
     */
    public function layanan()
    {
        return $this->hasMany(Layanan::class, 'id_owner');
    }

    /**
     * Scope a query to only include active owners.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}