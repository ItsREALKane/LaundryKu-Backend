<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'phone'];

    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_user');
    }

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class, 'id_user');
    }
}
