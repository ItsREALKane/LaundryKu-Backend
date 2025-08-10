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
        'tipe',
        'waktu_pengerjaan',
        'id_owner'
    ];

    protected $casts = [
        'waktu_pengerjaan' => 'integer', // dalam jam
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }

    /**
     * Get the pesanan for this layanan.
     */
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_layanan');
    }
} 