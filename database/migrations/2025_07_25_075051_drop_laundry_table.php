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
        Schema::dropIfExists('laundry');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('laundry', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('alamat');
            $table->string('phone');
            $table->string('img')->nullable();
            $table->enum('pengantaran', ['Ya', 'Tidak'])->default('Tidak');
            $table->timestamps();
        });
    }
};
