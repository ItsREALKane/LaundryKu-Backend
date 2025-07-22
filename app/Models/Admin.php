<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'admins';
    protected $fillable = ['name','email','nomor','status','id_owner','password',];

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }
}
