<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admins';
    protected $fillable = ['name','id_laundry','password',];

    public function laundry()
    {
        return $this->belongsTo(Laundry::class, 'id_laundry');
    }
}
