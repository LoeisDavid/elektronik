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
        Schema::create('acr', function (Blueprint $table) {
            $table->string('REF_NO', 10)->primary();
            $table->date('DT');
            $table->string('CST', 10);
            $table->string('SLS', 10);
            $table->double('BRT', 12, 2);
            $table->integer('POT_1')->nullable();
            $table->integer('POT_2')->nullable();
            $table->decimal('POT_R', 10, 2)->nullable();
            $table->string('USER', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acr');
    }
};
