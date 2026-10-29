<?php

namespace Database\Seeders;

use App\Services\DatabaseSwitcher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PresentationSeeder extends Seeder
{
    use SeedsStoreData;

    /**
     * Skenario Presentasi: 15 records (rentang 10-20 data).
     * - DB 1 (DB_DATABASE  / elektronik)  : Produk Elektronik (harga beli & jual terjangkau)
     * - DB 2 (DB_DATABASE2 / elektronik2) : Sparepart Elektronik (harga beli & jual terjangkau)
     */
    public function run(): void
    {
        $databases = DatabaseSwitcher::getDatabases();
        $db1 = $databases[0] ?? env('DB_DATABASE', 'elektronik');
        $db2 = $databases[1] ?? env('DB_DATABASE2');

        // =====================================================================
        // 1. SEED DB 1: ELEKTRONIK
        // =====================================================================
        DatabaseSwitcher::switch($db1);

        $this->command?->info("==================================================");
        $this->command?->info("   DB 1 ({$db1}): SKENARIO PRESENTASI ELEKTRONIK  ");
        $this->command?->info("==================================================");

        $this->seedData(totalRecords: 15, kasCount: 15, openCashierToday: false, mode: 'elektronik');

        // =====================================================================
        // 2. SEED DB 2: SPAREPART ELEKTRONIK (Jika DB 2 dikonfigurasi)
        // =====================================================================
        if (!empty($db2) && $db2 !== $db1) {
            try {
                // Pastikan database ke-2 sudah dibuat di MySQL
                $charset = config('database.connections.mysql.charset', 'utf8mb4');
                $collation = config('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
                DB::statement("CREATE DATABASE IF NOT EXISTS `{$db2}` CHARACTER SET {$charset} COLLATE {$collation}");

                DatabaseSwitcher::switch($db2);

                // Jalankan migrasi pada DB 2 jika tabel belum lengkap
                if (!Schema::hasTable('itm') || !Schema::hasTable('itmdt') || !Schema::hasTable('acr')) {
                    $this->command?->info("Menjalankan migrasi tabel pada DB 2 ({$db2})...");
                    Artisan::call('migrate', ['--force' => true]);
                }

                $this->command?->info("==================================================");
                $this->command?->info("   DB 2 ({$db2}): SKENARIO SPAREPART ELEKTRONIK   ");
                $this->command?->info("==================================================");

                $this->seedData(totalRecords: 15, kasCount: 15, openCashierToday: false, mode: 'sparepart');
            } catch (\Throwable $e) {
                $this->command?->warn("Gagal melakukan seeding pada DB 2 ({$db2}): " . $e->getMessage());
            } finally {
                // Kembalikan koneksi default ke DB 1
                DatabaseSwitcher::switch($db1);
            }
        }

        $this->command?->info("==================================================");
        $this->command?->info("   SKENARIO PRESENTASI (DB 1 & DB 2) BERHASIL!    ");
        $this->command?->info("==================================================");
    }
}
