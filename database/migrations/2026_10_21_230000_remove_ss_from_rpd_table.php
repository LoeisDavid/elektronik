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
        if (Schema::hasColumn('rpd', 'SS')) {
            Schema::table('rpd', function (Blueprint $table) {
                $table->dropColumn('SS');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasColumn('rpd', 'SS')) {
            Schema::table('rpd', function (Blueprint $table) {
                $table->string('SS', 15)->nullable();
            });
        }
    }
};
