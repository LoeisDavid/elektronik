<?php

namespace App\Console\Commands;

use App\Services\DatabaseSwitcher;
use Database\Seeders\TestSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DbTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test {database? : Nama database target (kosongkan untuk semua database)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengisi database dengan dataset besar 1000 data per tabel untuk pengujian performa (Multi-DB).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $targetDb = $this->argument('database');
        $allDbs = DatabaseSwitcher::getDatabases();

        if ($targetDb) {
            $databases = [$targetDb];
        } else {
            $databases = $allDbs;
        }

        $this->info("==================================================");
        $this->info("   MEMULAI SEEDING DATA TEST (1000 DATA/TABEL)    ");
        $this->info(" Target Database: " . implode(', ', $databases));
        $this->info("==================================================");

        $defaultConn = config('database.default', 'mysql');
        $originalDb = Config::get("database.connections.{$defaultConn}.database");

        foreach ($databases as $db) {
            $this->newLine();
            $this->info(">>> Memproses Database: [{$db}]");

            // Pastikan database ada di MySQL
            try {
                DB::statement("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {
                // Abaikan jika tidak punya hak
            }

            Config::set("database.connections.{$defaultConn}.database", $db);
            DB::purge($defaultConn);
            DB::reconnect($defaultConn);

            $seeder = new TestSeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->info(">>> Selesai seeding data test untuk [{$db}].");
        }

        // Kembalikan ke database asal
        Config::set("database.connections.{$defaultConn}.database", $originalDb);
        DB::purge($defaultConn);
        DB::reconnect($defaultConn);

        $this->newLine();
        $this->info("==================================================");
        $this->info(" DATA TEST BERHASIL DIBUAT DI SEMUA DATABASE!");
        $this->info(" User Login: admin | Password: admin123");
        $this->info("==================================================");

        return Command::SUCCESS;
    }
}
