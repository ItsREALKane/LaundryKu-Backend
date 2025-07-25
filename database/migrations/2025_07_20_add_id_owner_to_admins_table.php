<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->foreignId('id_owner')->nullable()->constrained('owners')->onDelete('set null');
            $table->string('email')->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            $table->dropForeign(['id_owner']);
            $table->dropColumn(['id_owner', 'email']);
        });
    }
};