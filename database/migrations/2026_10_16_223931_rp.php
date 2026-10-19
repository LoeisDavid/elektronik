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
        Schema::create('rp', function (Blueprint $table) {
            $table->string('RP', 15)->primary();
            $table->date('DTRQ');
            $table->date('DTCF1');
            $table->date('DTCF2');
            $table->string('SS', 15);
            $table->string('RQ', 15);
            $table->string('CF1', 15);
            $table->string('CF2', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rp');
    }
};
