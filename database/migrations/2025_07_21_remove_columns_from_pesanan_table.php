<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
            $table->dropForeign(['id_laundry']);
            $table->dropColumn([
                'id_user',
                'id_laundry',
                'tanggal_pesanan',
                'waktu_ambil',
                'info_pesanan',
                'pengiriman'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            $table->date('tanggal_pesanan');
            $table->dateTime('waktu_ambil')->nullable();
            $table->string('info_pesanan')->nullable();
            $table->string('pengiriman')->nullable();
        });
    }
};