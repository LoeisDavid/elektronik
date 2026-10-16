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
        Schema::create('rpd', function (Blueprint $table) {
            $table->string('RPD', 15)->primary();
            $table->string('RP', 15);
            $table->string('BC_OUT', 15);
            $table->string('SS', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rpd');
    }
};
