<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailHarga extends Model
{
    use HasFactory;

    protected $fillable = [
        'harga_id',
        'nama_item',
        'harga',
        'satuan'
    ];

    public function harga(): BelongsTo
    {
        return $this->belongsTo(Harga::class);
    }
}
