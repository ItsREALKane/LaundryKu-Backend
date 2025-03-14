<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laundry extends Model
{
    use HasFactory;
    protected $table = 'laundry'; // Pastikan sesuai dengan tabel di database
    protected $fillable = ['nama', 'alamat', 'nomor', 'img', 'rating', 'jasa'];

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
}
