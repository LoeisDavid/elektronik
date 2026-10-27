<?php

namespace App\Console\Commands;

use Database\Seeders\TestSeeder;
use Illuminate\Console\Command;

class DbTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengisi database dengan dataset besar (hingga 1000 data per tabel) untuk pengujian performa.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("==================================================");
        $this->info("   MEMULAI SEEDING DATA TEST (1000 DATA/TABEL)    ");
        $this->info("==================================================");

        $seeder = new TestSeeder();
        $seeder->setCommand($this);
        $seeder->run();

        $this->info("\n==================================================");
        $this->info(" DATA TEST (1000 DATA/TABEL) BERHASIL DIBUAT!");
        $this->info(" User Login: admin | Password: admin123");
        $this->info("==================================================");

        return Command::SUCCESS;
    }
}
