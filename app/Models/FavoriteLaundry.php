<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FavoriteLaundry extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'id_laundry'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function laundry()
    {
        return $this->belongsTo(Laundry::class, 'id_laundry');
    }
}
