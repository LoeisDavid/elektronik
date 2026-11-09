<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Make AL, TLP, and DS nullable on table cst
        Schema::table('cst', function (Blueprint $table) {
            $table->string('AL', 100)->nullable()->change();
            $table->string('TLP', 20)->nullable()->change();
            $table->string('DS', 15)->nullable()->change();
        });

        // 2. Migrate existing data from ds table into cst table if ds exists
        if (Schema::hasTable('ds')) {
            $desas = DB::table('ds')->get();
            foreach ($desas as $desa) {
                $exists = DB::table('cst')->where('CST', $desa->DS)->exists();
                if (!$exists) {
                    DB::table('cst')->insert([
                        'CST'        => $desa->DS,
                        'DES'        => $desa->DES,
                        'AL'         => null,
                        'TLP'        => null,
                        'DS'         => null,
                        'created_at' => $desa->created_at ?? now(),
                        'updated_at' => $desa->updated_at ?? now(),
                    ]);
                }
            }

            // 3. Drop table ds
            Schema::dropIfExists('ds');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('ds')) {
            Schema::create('ds', function (Blueprint $table) {
                $table->string('DS', 15)->primary();
                $table->string('DES', 50)->unique();
                $table->timestamps();
            });

            // Re-populate ds from cst where DS is null
            $desas = DB::table('cst')->whereNull('DS')->get();
            foreach ($desas as $desa) {
                DB::table('ds')->insertOrIgnore([
                    'DS'         => $desa->CST,
                    'DES'        => substr($desa->DES, 0, 50),
                    'created_at' => $desa->created_at,
                    'updated_at' => $desa->updated_at,
                ]);
            }
        }
    }
};
