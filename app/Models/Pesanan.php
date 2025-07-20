<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';
    protected $fillable = [
        'id_user',
        'id_owner',
        'tanggal_pesanan',
        'status',
        'total_harga',
        'alamat',
        'waktu_ambil',
        'catatan',
        'info_pesanan',
        'pengiriman',
        'jenis_pembayaran',
        'tgl_langganan_berakhir',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan');
    }

    public function tagihan()
    {
        return $this->hasOne(Tagihan::class, 'id_pesanan');
    }
}
