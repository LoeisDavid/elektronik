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
        Schema::create('kas', function (Blueprint $table) {
            $table->string('CD', 20)->primary();
            $table->date('TGL');
            $table->string('USER', 10);
            $table->integer('AWLT');
            $table->integer('AWLNT');
            $table->integer('DBT')->nullable();
            $table->integer('DBNT')->nullable();
            $table->integer('CRT')->nullable();
            $table->integer('CRNT')->nullable();
            $table->integer('AKHT')->nullable();
            $table->integer('AKHNT')->nullable();
            $table->dateTime('TGLM');
            $table->dateTime('TGLK')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas');
    }
};
