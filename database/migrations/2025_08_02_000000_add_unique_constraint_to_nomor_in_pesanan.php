<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Menambahkan unique constraint pada kolom nomor di tabel pesanan
        // Tetapi hanya unique per id_owner, sehingga nomor yang sama bisa digunakan
        // oleh owner yang berbeda
        Schema::table('pesanan', function (Blueprint $table) {
            // Hapus indeks yang mungkin sudah ada untuk menghindari konflik
            if (Schema::hasIndex('pesanan', 'pesanan_nomor_id_owner_unique')) {
                $table->dropIndex('pesanan_nomor_id_owner_unique');
            }
            
            // Tambahkan unique constraint pada kombinasi nomor dan id_owner
            $table->unique(['nomor', 'id_owner'], 'pesanan_nomor_id_owner_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Hapus unique constraint
            if (Schema::hasIndex('pesanan', 'pesanan_nomor_id_owner_unique')) {
                $table->dropIndex('pesanan_nomor_id_owner_unique');
            }
        });
    }
};