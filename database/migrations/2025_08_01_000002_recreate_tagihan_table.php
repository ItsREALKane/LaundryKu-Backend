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
        // Drop existing tagihan table
        Schema::dropIfExists('tagihan');
        
        // Create new tagihan table with updated structure
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelanggan'); // dari table pesanan
            $table->string('nomor'); // dari table pesanan
            $table->text('alamat'); // dari table pesanan
            $table->integer('jumlah_pesanan'); // total semua pesanan pelanggan
            $table->decimal('total_tagihan', 10, 2); // Total jumlah harga dari semua pesanan pelanggan yang belum lunas
            $table->foreignId('id_owner')->constrained('owners')->onDelete('cascade'); // ID laundry pemilik (buat filter)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop new tagihan table
        Schema::dropIfExists('tagihan');
        
        // Recreate original tagihan table
        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->decimal('total_tagihan', 10, 2);
            $table->timestamps();
        });
    }
};