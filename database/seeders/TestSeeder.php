<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestSeeder extends Seeder
{
    use SeedsStoreData;

    /**
     * Skenario Data Test: 1000 records.
     * Digunakan untuk stress test pagination, performa query/search,
     * dan penanganan dataset besar.
     */
    public function run(): void
    {
        $this->command?->info("==================================================");
        $this->command?->info("   MENJALANKAN SKENARIO DATA TEST (1000 DATA)    ");
        $this->command?->info("==================================================");

        $this->seedData(totalRecords: 1000, kasCount: 60, openCashierToday: false);

        $this->command?->info("==================================================");
        $this->command?->info("   SKENARIO DATA TEST (1000 DATA) BERHASIL!      ");
        $this->command?->info("==================================================");
    }
}
