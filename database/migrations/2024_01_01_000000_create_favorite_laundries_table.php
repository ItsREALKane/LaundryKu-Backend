<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('favorite_laundries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_laundry')->constrained('laundry')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['id_user', 'id_laundry']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorite_laundries');
    }
};
