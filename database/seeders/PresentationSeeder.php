<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PresentationSeeder extends Seeder
{
    use SeedsStoreData;

    /**
     * Skenario Presentasi: 15 records (rentang 10-20 data).
     * Ringan, bersih, mudah didemokan.
     * Semua riwayat kasir sebelumnya sudah tertutup rapi sehingga
     * presenter dapat mendemonstrasikan alur "Buka Kasir" secara langsung.
     */
    public function run(): void
    {
        $this->command?->info("==================================================");
        $this->command?->info("   MENJALANKAN SKENARIO PRESENTASI (15 DATA)     ");
        $this->command?->info("==================================================");

        $this->seedData(totalRecords: 15, kasCount: 15, openCashierToday: false);

        $this->command?->info("==================================================");
        $this->command?->info("   SKENARIO PRESENTASI (15 DATA) BERHASIL!        ");
        $this->command?->info("==================================================");
    }
}
