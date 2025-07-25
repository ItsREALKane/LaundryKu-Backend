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
            // Ubah kolom jenis_pembayaran menjadi enum dengan pilihan cash dan transfer
            if (Schema::hasColumn('pesanan', 'jenis_pembayaran')) {
                $table->enum('jenis_pembayaran', ['cash', 'transfer'])->default('cash')->nullable(false)->change();
            }
            
            // Set default value untuk jumlah_harga jika belum ada
            if (Schema::hasColumn('pesanan', 'jumlah_harga')) {
                $table->decimal('jumlah_harga', 10, 2)->default(0)->nullable(false)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Kembalikan ke nullable
            if (Schema::hasColumn('pesanan', 'jenis_pembayaran')) {
                $table->enum('jenis_pembayaran', ['cash', 'transfer'])->nullable()->change();
            }
            
            if (Schema::hasColumn('pesanan', 'jumlah_harga')) {
                $table->decimal('jumlah_harga', 10, 2)->nullable()->change();
            }
        });
    }
};