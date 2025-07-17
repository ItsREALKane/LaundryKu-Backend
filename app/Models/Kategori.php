<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    
    protected $fillable = [
        'jenis_kategori',
        'deskripsi',
        'icon'
    ];

    protected $attributes = [
        'icon' => 'local_laundry_service' // Default Material Icon
    ];

    public function laundries()
    {
        return $this->belongsToMany(Laundry::class, 'laundry_kategori', 'id_kategori', 'id_laundry');
    }

    public function getJenisKategoriAttribute($value)
    {
        return ucwords($value); // Kapitalisasi tiap kata
    }

    public function setJenisKategoriAttribute($value)
    {
        $this->attributes['jenis_kategori'] = strtolower($value); // Simpen dalam lowercase
    }
}
