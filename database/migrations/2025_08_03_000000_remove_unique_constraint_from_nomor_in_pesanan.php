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
        // Menghapus unique constraint pada kolom nomor di tabel pesanan
        // Sehingga nomor telepon yang sama bisa digunakan berkali-kali
        Schema::table('pesanan', function (Blueprint $table) {
            // Hapus unique constraint jika ada
            if (Schema::hasIndex('pesanan', 'pesanan_nomor_id_owner_unique')) {
                $table->dropIndex('pesanan_nomor_id_owner_unique');
            }
            
            // Hapus unique constraint lainnya jika ada
            if (Schema::hasIndex('pesanan', 'pesanan_nomor_unique')) {
                $table->dropIndex('pesanan_nomor_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Mengembalikan unique constraint pada kombinasi nomor dan id_owner
            $table->unique(['nomor', 'id_owner'], 'pesanan_nomor_id_owner_unique');
        });
    }
};