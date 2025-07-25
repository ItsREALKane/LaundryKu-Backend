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
        Schema::table('hargas', function (Blueprint $table) {
            if (Schema::hasColumn('hargas', 'id_laundry')) {
                $table->dropForeign(['id_laundry']);
                $table->dropColumn('id_laundry');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hargas', function (Blueprint $table) {
            if (!Schema::hasColumn('hargas', 'id_laundry')) {
                $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            }
        });
    }
};
