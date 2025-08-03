<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    use HasFactory;

    protected $table = 'layanan';
    
    protected $fillable = [
        'nama_layanan',
        'harga_layanan',
        'keterangan_layanan',
        'id_owner',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the owner that owns the layanan.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }

    /**
     * Get the pesanan that use this layanan.
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'layanan', 'nama_layanan');
    }
}
