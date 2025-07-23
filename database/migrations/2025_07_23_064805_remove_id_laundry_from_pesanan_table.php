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
            // Cek apakah kolom id_laundry masih ada
            if (Schema::hasColumn('pesanan', 'id_laundry')) {
                // Cek apakah foreign key masih ada
                try {
                    $table->dropForeign(['id_laundry']);
                } catch (\Exception $e) {
                    // Foreign key mungkin sudah dihapus sebelumnya
                }
                // Kemudian hapus kolom
                $table->dropColumn('id_laundry');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            // Kembalikan kolom jika diperlukan rollback
            $table->foreignId('id_laundry')->after('id_user')->nullable()->constrained('laundry')->onDelete('cascade');
        });
    }
};
