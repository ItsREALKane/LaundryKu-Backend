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
            if (!Schema::hasColumn('hargas', 'id_owner')) {
                $table->foreignId('id_owner')->after('id')->nullable()->constrained('owners')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hargas', function (Blueprint $table) {
            if (Schema::hasColumn('hargas', 'id_owner')) {
                $table->dropForeign(['id_owner']);
                $table->dropColumn('id_owner');
            }
        });
    }
};
