<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihan';
    protected $fillable = [
        'id_user',
        'id_laundry',
        'id_pesanan',
        'total_tagihan',
        'status_pembayaran',
        'metode_pembayaran',
        'bukti_pembayaran',
        'catatan'
    ];

    protected $casts = [
        'total_tagihan' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function laundry()
    {
        return $this->belongsTo(Laundry::class, 'id_laundry');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }

    public function getStatusPembayaranAttribute($value)
    {
        return $value ?: 'pending';
    }

    public function getMetodePembayaranAttribute($value)
    {
        return $value ?: 'cash';
    }
}
