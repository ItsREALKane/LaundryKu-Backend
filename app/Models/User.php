<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

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
