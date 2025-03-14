<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryKategori extends Model
{
    use HasFactory;

    protected $table = 'laundry_kategori';
    protected $fillable = ['id_laundry', 'id_kategori'];
}
