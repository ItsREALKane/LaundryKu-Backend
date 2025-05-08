<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Harga extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_laundry',
        'jenis_harga'
    ];

    public function laundry(): BelongsTo
    {
        return $this->belongsTo(Laundry::class);
    }

    public function detailHargas(): HasMany
    {
        return $this->hasMany(DetailHarga::class);
    }
}
