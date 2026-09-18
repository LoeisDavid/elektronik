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
        Schema::create('users', function (Blueprint $table) {
            $table->string('USERNAME', 20)->primary();
            $table->string('PASS', 255);
            $table->string('AL', 100)->nullable();
            $table->string('HP', 25)->nullable();
            $table->string('DES', 100)->nullable();
            $table->integer('ROLE')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
