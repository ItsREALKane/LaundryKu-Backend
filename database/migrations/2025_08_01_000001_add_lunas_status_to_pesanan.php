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
        // Modify the status enum in pesanan table to include 'lunas'
        DB::statement("ALTER TABLE pesanan MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai', 'lunas') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE pesanan MODIFY COLUMN status ENUM('pending', 'diproses', 'selesai') DEFAULT 'pending'");
    }
};