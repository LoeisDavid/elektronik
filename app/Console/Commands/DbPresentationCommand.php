<?php

namespace App\Console\Commands;

use Database\Seeders\PresentationSeeder;
use Illuminate\Console\Command;

class DbPresentationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:presentation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengisi database dengan data dummy alat elektronik untuk presentasi (15-20 data per tabel).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("==================================================");
        $this->info("   MEMULAI SEEDING DATA PRESENTASI (15-20 DATA)   ");
        $this->info("==================================================");

        $seeder = new PresentationSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->info("\n==================================================");
        $this->info(" DATA PRESENTASI BERHASIL DIBUAT!");
        $this->info(" User Login: admin | Password: admin123");
        $this->info("==================================================");

        return Command::SUCCESS;
    }
}
