<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database aplikasi.
     * Default sekarang menggunakan skenario Presentasi (15 data, rentang 10-20).
     *
     * Cara Menjalankan Skenario:
     * 1. Skenario Presentasi (10-20 data):
     *    php artisan db:seed
     *    ATAU: php artisan db:seed --class=PresentationSeeder
     *
     * 2. Skenario Data Test (Banyak Data / 1000 data):
     *    php artisan db:seed --class=TestSeeder
     */
    public function run(): void
    {
        $scenario = env('SEED_SCENARIO', 'presentation');

        if (strtolower($scenario) === 'test') {
            $this->call(TestSeeder::class);
        } else {
            $this->call(PresentationSeeder::class);
        }
    }
}
