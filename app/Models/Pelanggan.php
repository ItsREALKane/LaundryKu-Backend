<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    
    protected $fillable = [
        'nama_pelanggan',
        'nomor',
        'alamat',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the pesanan for this pelanggan.
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan');
    }

    /**
     * Scope a query to search pelanggan by name or number.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('nama_pelanggan', 'like', "%{$search}%")
                    ->orWhere('nomor', 'like', "%{$search}%");
    }
} 