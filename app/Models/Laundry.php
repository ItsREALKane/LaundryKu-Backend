<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laundry extends Model
{
    use HasFactory;
    protected $table = 'laundry';
    
    protected $fillable = [
        'nama',
        'alamat',
        'nomor',
        'img',
        'rating',
        'jasa',
        'pengantaran',
        'status',
        'jam_buka',
        'jam_tutup',
        'deskripsi'
    ];

    protected $casts = [
        'rating' => 'decimal:1',
        'jam_buka' => 'datetime:H:i',
        'jam_tutup' => 'datetime:H:i',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $attributes = [
        'status' => 'buka',
        'rating' => 0.0,
        'pengantaran' => 'tidak'
    ];

    public function admins()
    {
        return $this->hasMany(Admin::class, 'id_laundry');
    }

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_laundry');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_laundry');
    }

    public function kategori()
    {
        return $this->belongsToMany(Kategori::class, 'laundry_kategori', 'id_laundry', 'id_kategori');
    }

    public function favoritedBy()
    {
        return $this->hasMany(FavoriteLaundry::class, 'id_laundry');
    }

    public function getImgAttribute($value)
    {
        if ($value && !str_starts_with($value, 'http')) {
            return asset('storage/' . $value);
        }
        return $value;
    }

    public function getStatusAttribute($value)
    {
        return $value ?: 'buka';
    }

    public function getPengantaranAttribute($value)
    {
        return $value ?: 'tidak';
    }
}
