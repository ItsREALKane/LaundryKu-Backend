<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTagihan extends Model
{
    use HasFactory;

    protected $table = 'detail_tagihan';
    
    protected $fillable = [
        'id_pesanan',
        'layanan',
        'berat',
        'jumlah_harga',
        'status',
        'id_owner',
        'nama_pelanggan'
    ];

    protected $casts = [
        'berat' => 'decimal:2',
        'jumlah_harga' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the pesanan that owns the detail tagihan.
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    /**
     * Get the owner that owns the detail tagihan.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }
}