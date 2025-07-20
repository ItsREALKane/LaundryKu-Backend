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
            if (!Schema::hasColumn('pesanan', 'info_pesanan')) {
                $table->text('info_pesanan')->nullable()->after('catatan');
            }
            if (!Schema::hasColumn('pesanan', 'pengiriman')) {
                $table->enum('pengiriman', ['pickup', 'delivery'])->nullable()->after('info_pesanan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->dropColumn(['info_pesanan', 'pengiriman']);
        });
    }
};