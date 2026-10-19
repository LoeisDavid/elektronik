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
        Schema::create('cst', function (Blueprint $table) {
            $table->string('CST', 10)->primary();
            $table->string('DES', 150)->collation('utf8mb4_unicode_ci')->unique();
            $table->string('TLP', 20)->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cst');
    }
};
