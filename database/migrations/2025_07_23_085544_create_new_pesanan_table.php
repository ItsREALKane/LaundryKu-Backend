<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Drop existing pesanan table if exists
        Schema::dropIfExists('pesanan');
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Create new pesanan table with updated structure
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_owner')->constrained('owners')->onDelete('cascade');
            $table->foreignId('id_admin')->nullable()->constrained('owners')->onDelete('set null'); // id_admin mengacu ke owner yang membuat akun
            $table->string('nama_pelanggan');
            $table->string('nomor');
            $table->text('alamat');
            $table->string('layanan');
            $table->decimal('berat', 8, 2)->nullable();
            $table->decimal('jumlah_harga', 10, 2)->nullable(); // boleh kosong di awal
            $table->enum('status', ['pending', 'diproses', 'selesai'])->default('pending');
            $table->enum('jenis_pembayaran', ['cash', 'transfer'])->nullable(); // boleh kosong di awal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
