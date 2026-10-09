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
        Schema::create('itm', function (Blueprint $table) {
            $table->string('ITM', 10)->primary();
            $table->string('DES', 150)->collation('utf8mb4_unicode_ci')->unique();
            $table->unsignedInteger('STOK');
            $table->decimal('HRG', 10, 2);
            $table->string('KTG', 10);
            $table->string('MRK', 10);
            $table->string('SPP', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itm');
    }
};
