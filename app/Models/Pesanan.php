<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\DetailTagihan;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    
    protected $fillable = [
        'id_owner',
        'id_admin',
        'id_pelanggan',
        'nama_pelanggan',
        'nomor',
        'alamat',
        'layanan',
        'berat',
        'jumlah_harga',
        'status',
        'jenis_pembayaran',
    ];

    protected $casts = [
        'berat' => 'decimal:2',
        'jumlah_harga' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the owner that owns the pesanan.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }
    
    /**
     * Get the admin that created the pesanan.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'id_admin');
    }

    /**
     * Get the pelanggan for this pesanan.
     */
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    /**
     * Get the detail tagihan for this pesanan.
     */
    public function detailTagihan()
    {
        return $this->hasOne(DetailTagihan::class, 'id_pesanan');
    }

    /**
     * Scope a query to only include pesanan with status 'lunas'.
     */
    public function scopeLunas($query)
    {
        return $query->where('status', 'lunas');
    }

    /**
     * Scope a query to only include pesanan with status not 'lunas'.
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status', '!=', 'lunas');
    }
}
