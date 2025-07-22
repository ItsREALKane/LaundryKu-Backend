<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn('tgl_langganan_berakhir');
            $table->decimal('berat', 8, 2)->before('created_at');
            $table->string('layanan')->before('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->date('tgl_langganan_berakhir')->nullable();
            $table->dropColumn(['berat', 'layanan']);
        });
    }
};