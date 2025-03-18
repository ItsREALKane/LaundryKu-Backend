<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Isi kolom timestamps dengan waktu sekarang jika masih NULL
        DB::statement('UPDATE laundry SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        DB::statement('UPDATE admins SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');
        DB::statement('UPDATE person SET created_at = NOW(), updated_at = NOW() WHERE created_at IS NULL OR updated_at IS NULL');

        Schema::table('person', function (Blueprint $table) {
            $table->string('phone')->nullable(); // Tambah kolom phone
        });

        Schema::table('pesanan', function (Blueprint $table) {
            $table->enum('jenis_pembayaran', ['sekali', 'langganan'])->after('total_harga');
            $table->date('tgl_langganan_berakhir')->nullable()->after('jenis_pembayaran');
        });
    }

    public function down(): void
    {
        // Tidak perlu rollback karena hanya mengupdate data
        Schema::table('person', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};

