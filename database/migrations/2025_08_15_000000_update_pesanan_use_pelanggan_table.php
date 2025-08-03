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
            // Tambah foreign key ke tabel pelanggan
            $table->foreignId('id_pelanggan')->nullable()->constrained('pelanggan')->onDelete('set null');
            
            // Hapus kolom yang sudah ada di tabel pelanggan
            $table->dropColumn(['nama_pelanggan', 'nomor', 'alamat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Hapus foreign key
            $table->dropForeign(['id_pelanggan']);
            $table->dropColumn('id_pelanggan');
            
            // Tambah kembali kolom yang dihapus
            $table->string('nama_pelanggan');
            $table->string('nomor');
            $table->text('alamat');
        });
    }
}; 