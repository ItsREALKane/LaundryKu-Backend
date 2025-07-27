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
        // Drop detail_pesanan table
        Schema::dropIfExists('detail_pesanan');
        
        // Create detail_tagihan table
        Schema::create('detail_tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->string('layanan'); // dari table pesanan
            $table->decimal('berat', 8, 2)->nullable(); // dari table pesanan
            $table->decimal('jumlah_harga', 10, 2)->nullable(); // dari table pesanan
            $table->enum('status', ['pending', 'diproses', 'selesai', 'lunas'])->default('pending'); // dari table pesanan dengan tambahan status 'lunas'
            $table->foreignId('id_owner')->constrained('owners')->onDelete('cascade');
            $table->string('nama_pelanggan'); // dari table pesanan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop detail_tagihan table
        Schema::dropIfExists('detail_tagihan');
        
        // Recreate detail_pesanan table
        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->string('pesanan');
            $table->decimal('harga_pesanan', 10, 2);
            $table->decimal('total_pesanan', 10, 2);
            $table->timestamps();
        });
    }
};