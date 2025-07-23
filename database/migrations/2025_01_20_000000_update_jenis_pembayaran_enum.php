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
        Schema::table('pesanan', function (Blueprint $table) {
            // Ubah kolom jenis_pembayaran menjadi enum dengan pilihan Cash dan Transfer
            $table->enum('jenis_pembayaran', ['Cash', 'Transfer'])->default('Cash')->change();
            
            // Set default value untuk total_harga jika belum ada
            $table->decimal('total_harga', 10, 2)->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Kembalikan ke string biasa
            $table->string('jenis_pembayaran')->change();
            $table->decimal('total_harga', 10, 2)->nullable()->change();
        });
    }
};