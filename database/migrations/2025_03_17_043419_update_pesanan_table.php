<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('pesanan', function (Blueprint $table) {
        if (!Schema::hasColumn('pesanan', 'jenis_pembayaran')) {
            $table->enum('jenis_pembayaran', ['sekali', 'langganan'])->after('total_harga');
        }
        if (!Schema::hasColumn('pesanan', 'tgl_langganan_berakhir')) {
            $table->date('tgl_langganan_berakhir')->nullable()->after('jenis_pembayaran');
        }
    });
}

public function down()
{
    Schema::table('pesanan', function (Blueprint $table) {
        $table->dropColumn(['jenis_pembayaran', 'tgl_langganan_berakhir']);
    });
}

};
