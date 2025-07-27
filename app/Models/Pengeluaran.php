<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    use HasFactory;

    protected $table = 'pengeluaran';
    
    protected $fillable = [
        'id_owner',
        'kategori',
        'jumlah',
        'keterangan',
        'tanggal'
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the owner that owns the pengeluaran.
     */
    public function owner()
    {
        return $this->belongsTo(Owner::class, 'id_owner');
    }
}