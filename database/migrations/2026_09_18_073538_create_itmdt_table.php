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
        Schema::create('itmdt', function (Blueprint $table) {
            $table->string('ITM', 10);
            $table->string('BC_IN', 20);
            $table->string('BC_OUT', 20)->primary();
            $table->date('TM');
            $table->decimal('HRB', 10, 2);
            $table->string('GD', 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itmdt');
    }
};
