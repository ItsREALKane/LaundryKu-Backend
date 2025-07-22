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
        Schema::table('admins', function (Blueprint $table) {
            // Check if id_laundry column exists before dropping
            if (Schema::hasColumn('admins', 'id_laundry')) {
                $table->dropForeign(['id_laundry']);
                $table->dropColumn('id_laundry');
            }
            
            // Add new columns if they don't exist
            if (!Schema::hasColumn('admins', 'nomor')) {
                $table->string('nomor')->after('email');
            }
            if (!Schema::hasColumn('admins', 'status')) {
                $table->string('status')->default('aktif')->after('nomor');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Add back id_laundry if it doesn't exist
            if (!Schema::hasColumn('admins', 'id_laundry')) {
                $table->foreignId('id_laundry')->after('name')->constrained('laundry')->onDelete('cascade');
            }
            
            // Drop new columns if they exist
            if (Schema::hasColumn('admins', 'nomor')) {
                $table->dropColumn('nomor');
            }
            if (Schema::hasColumn('admins', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
