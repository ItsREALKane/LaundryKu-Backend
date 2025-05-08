<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_hargas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('harga_id')->constrained('hargas')->onDelete('cascade');
            $table->string('nama_item');
            $table->decimal('harga', 10, 2);
            $table->string('satuan')->nullable(); // kg, pcs, pasang
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_hargas');
    }
};
