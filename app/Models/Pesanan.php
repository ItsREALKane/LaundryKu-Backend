<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Owner;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $fillable = [
        'id_owner',
        'id_admin',
        'nama_pelanggan',
        'nomor',
        'alamat',
        'layanan',
        'berat',
        'jumlah_harga',
        'status',
        'jenis_pembayaran',
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
}
