<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('laundry', function (Blueprint $table) { // Pindahkan sebelum `admins`
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('nomor');
            $table->text('img');
            $table->decimal('rating', 3, 2);
            $table->string('jasa');
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('kategori', function (Blueprint $table) { // Pindahkan sebelum `laundry_kategori`
            $table->id();
            $table->string('jenis_kategori');
            $table->timestamps();
        });

        Schema::create('laundry_kategori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            $table->foreignId('id_kategori')->constrained('kategori')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            $table->dateTime('tanggal_pesanan');
            $table->string('status');
            $table->decimal('total_harga', 10, 2);
            $table->text('alamat');
            $table->time('waktu_ambil');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->string('pesanan');
            $table->decimal('harga_pesanan', 10, 2);
            $table->decimal('total_pesanan', 10, 2);
            $table->timestamps();
        });

        Schema::create('tagihan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            $table->foreignId('id_pesanan')->constrained('pesanan')->onDelete('cascade');
            $table->decimal('total_tagihan', 10, 2);
            $table->timestamps();
        });
    }

    /**9
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan');
        Schema::dropIfExists('detail_pesanan');
        Schema::dropIfExists('pesanan');
        Schema::dropIfExists('laundry_kategori');
        Schema::dropIfExists('kategori');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('laundry');
        Schema::dropIfExists('users');
    }
};
