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
        Schema::create('sts', function (Blueprint $table) {
            $table->string('REC', 3)->primary();
            $table->string('REF_NO', 10);
            $table->string('BC_OUT', 20);
            $table->string('ITM', 10);
            $table->decimal('HGJ', 16, 2);
            $table->string('ST', 3);
            $table->integer('POT_1')->nullable();
            $table->integer('POT_2')->nullable();
            $table->decimal('POT_R', 10, 2)->nullable();
            $table->integer('PPN')->nullable();
            $table->decimal('BRT', 14, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sts');
    }
};
