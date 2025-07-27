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
        // Update existing decimal values to integers (remove decimal places)
        DB::statement('UPDATE pesanan SET jumlah_harga = ROUND(jumlah_harga)');
        
        // Change column type from decimal to integer
        Schema::table('pesanan', function (Blueprint $table) {
            $table->integer('jumlah_harga')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Change column type back to decimal
        Schema::table('pesanan', function (Blueprint $table) {
            $table->decimal('jumlah_harga', 10, 2)->nullable()->change();
        });
    }
};