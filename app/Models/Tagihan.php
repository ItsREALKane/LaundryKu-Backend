<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    
    protected $fillable = [
        'nama_pelanggan',
        'nomor',
        'alamat',
        'jumlah_pesanan',
        'total_tagihan',
        'id_owner'
    ];

    protected $casts = [
        'jumlah_pesanan' => 'integer',
        'total_tagihan' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the owner that owns the tagihan.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }

    /**
     * Get the detail tagihan for this tagihan.
     */
    public function detailTagihan()
    {
        return $this->hasMany(DetailTagihan::class, 'id_owner', 'id_owner')
                    ->where('nama_pelanggan', $this->nama_pelanggan)
                    ->where('status', '!=', 'lunas');
    }
}
