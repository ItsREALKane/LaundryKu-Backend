<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('position');
            $table->decimal('salary', 10, 2);
            $table->date('join_date');
            $table->string('status');
            $table->string('avatar')->nullable();
            $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person');
    }
};
