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
            // Tambah kembali kolom pelanggan di tabel pesanan
            $table->string('nama_pelanggan')->after('id_admin');
            $table->string('nomor')->after('nama_pelanggan');
            $table->text('alamat')->after('nomor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Hapus kolom yang ditambahkan
            $table->dropColumn(['nama_pelanggan', 'nomor', 'alamat']);
        });
    }
}; 