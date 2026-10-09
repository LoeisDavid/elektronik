<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Konversi nilai TGLM/TGLK yang selama ini tersimpan sebagai UTC
     * menjadi waktu lokal Asia/Jakarta (WIB = UTC+7).
     *
     * Dijalankan SEKALI — aman di-rerun karena penanda di tabel migrations.
     */
    public function up(): void
    {
        // TGLM tidak nullable
        DB::statement("UPDATE `kas` SET `TGLM` = DATE_ADD(`TGLM`, INTERVAL 7 HOUR)");
        // TGLK nullable — hanya yang sudah terisi
        DB::statement("UPDATE `kas` SET `TGLK` = DATE_ADD(`TGLK`, INTERVAL 7 HOUR) WHERE `TGLK` IS NOT NULL");
        // Sinkronkan kolom TGL (date) dengan tanggal lokal dari TGLM
        DB::statement("UPDATE `kas` SET `TGL` = DATE(`TGLM`)");
    }

    public function down(): void
    {
        DB::statement("UPDATE `kas` SET `TGLM` = DATE_SUB(`TGLM`, INTERVAL 7 HOUR)");
        DB::statement("UPDATE `kas` SET `TGLK` = DATE_SUB(`TGLK`, INTERVAL 7 HOUR) WHERE `TGLK` IS NOT NULL");
        DB::statement("UPDATE `kas` SET `TGL` = DATE(`TGLM`)");
    }
};
