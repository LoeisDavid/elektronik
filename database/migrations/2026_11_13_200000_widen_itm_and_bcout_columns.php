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
        Schema::table('itm', function (Blueprint $table) {
            $table->string('ITM', 25)->change();
        });

        Schema::table('itmdt', function (Blueprint $table) {
            $table->string('ITM', 25)->change();
            $table->string('BC_IN', 40)->change();
            $table->string('BC_OUT', 40)->change();
        });

        Schema::table('sts', function (Blueprint $table) {
            $table->string('ITM', 25)->change();
            $table->string('BC_OUT', 40)->change();
        });

        Schema::table('rpd', function (Blueprint $table) {
            $table->string('BC_OUT', 40)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itm', function (Blueprint $table) {
            $table->string('ITM', 10)->change();
        });

        Schema::table('itmdt', function (Blueprint $table) {
            $table->string('ITM', 10)->change();
            $table->string('BC_IN', 20)->change();
            $table->string('BC_OUT', 20)->change();
        });

        Schema::table('sts', function (Blueprint $table) {
            $table->string('ITM', 10)->change();
            $table->string('BC_OUT', 20)->change();
        });

        Schema::table('rpd', function (Blueprint $table) {
            $table->string('BC_OUT', 20)->change();
        });
    }
};
