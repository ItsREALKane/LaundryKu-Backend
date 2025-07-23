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
        // Cek dan hapus tabel jika ada
        if (Schema::hasTable('detail_hargas')) {
            Schema::dropIfExists('detail_hargas');
        }
        
        if (Schema::hasTable('hargas')) {
            Schema::dropIfExists('hargas');
        }
        
        if (Schema::hasTable('laundry_kategori')) {
            Schema::dropIfExists('laundry_kategori');
        }
        
        if (Schema::hasTable('kategori')) {
            Schema::dropIfExists('kategori');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });

        Schema::create('laundry_kategori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_owner')->constrained('owners')->onDelete('cascade');
            $table->foreignId('id_kategori')->constrained('kategori')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('hargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_owner')->constrained('owners')->onDelete('cascade');
            $table->string('nama');
            $table->decimal('harga', 10, 2);
            $table->timestamps();
        });

        Schema::create('detail_hargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_harga')->constrained('hargas')->onDelete('cascade');
            $table->string('nama');
            $table->decimal('harga', 10, 2);
            $table->timestamps();
        });
    }
};
