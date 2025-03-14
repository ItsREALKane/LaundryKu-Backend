<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';
    protected $fillable = ['jenis_kategori'];

    public function laundries()
    {
        return $this->belongsToMany(Laundry::class, 'laundry_kategori', 'id_kategori', 'id_laundry');
    }
}
