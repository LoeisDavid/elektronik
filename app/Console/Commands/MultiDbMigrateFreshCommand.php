<?php

namespace App\Console\Commands;

use App\Services\DatabaseSwitcher;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'migrate:fresh')]
class MultiDbMigrateFreshCommand extends Command
{
    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:fresh
                {--database= : Nama database target (kosongkan untuk semua database)}
                {--drop-views : Drop all tables and views}
                {--drop-types : Drop all tables and types (Postgres only)}
                {--force : Force the operation to run when in production}
                {--path=* : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--schema-path= : The path to a schema dump file}
                {--seed : Indicates if the seed task should be re-run}
                {--seeder= : The class name of the root seeder}
                {--step : Force the migrations to be run so they can be rolled back individually}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all tables and re-run all migrations across all configured databases (Multi-DB).';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!$this->confirmToProceed()) {
            return self::FAILURE;
        }

        $allDbs = DatabaseSwitcher::getDatabases();
        $targetDb = $this->option('database');

        if ($targetDb) {
            $databases = [$targetDb];
        } else {
            $databases = $allDbs;
        }

        $this->info("==================================================");
        $this->info(" MIGRATE FRESH MULTI-DATABASE (" . count($databases) . " DATABASE)");
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
                // Abaikan jika tidak memiliki hak create database
            }

            // Ganti koneksi ke database target
            Config::set("database.connections.{$defaultConn}.database", $db);
            DB::purge($defaultConn);
            DB::reconnect($defaultConn);

            // 1. Wipe database (drop all tables)
            $this->components->task("Wiping all tables on [{$db}]", function () use ($defaultConn) {
                return $this->callSilent('db:wipe', [
                    '--database'   => $defaultConn,
                    '--drop-views' => $this->option('drop-views'),
                    '--drop-types' => $this->option('drop-types'),
                    '--force'      => true,
                ]) === 0;
            });

            // 2. Migrate
            $this->call('migrate', array_filter([
                '--database'    => $defaultConn,
                '--path'        => $this->option('path'),
                '--realpath'    => $this->option('realpath'),
                '--schema-path' => $this->option('schema-path'),
                '--force'       => true,
                '--step'        => $this->option('step'),
            ]));

            // 3. Seeding jika diminta
            if ($this->option('seed')) {
                $this->call('db:seed', array_filter([
                    '--database' => $defaultConn,
                    '--class'    => $this->option('seeder') ?: 'DatabaseSeeder',
                    '--force'    => true,
                ]));
            }

            $this->info(">>> Selesai migrate:fresh untuk [{$db}].");
        }

        // Kembalikan ke database asal
        Config::set("database.connections.{$defaultConn}.database", $originalDb);
        DB::purge($defaultConn);
        DB::reconnect($defaultConn);

        $this->newLine();
        $this->info("==================================================");
        $this->info(" SEMUA DATABASE BERHASIL DI-MIGRATE FRESH!");
        $this->info("==================================================");

        return self::SUCCESS;
    }
}
