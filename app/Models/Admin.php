<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens, HasFactory;
    protected $table = 'admins';
    protected $fillable = ['name','id_laundry','password',];

    public function laundry()
    {
        return $this->belongsTo(Laundry::class, 'id_laundry');
    }
}
