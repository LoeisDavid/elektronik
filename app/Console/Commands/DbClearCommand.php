<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DatabaseSwitcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DbClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear {database? : Nama database target (kosongkan untuk semua database)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus seluruh data di database dan hanya menyisakan satu user admin default (Multi-DB).';

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
        $this->info("       MENGOSONGKAN SELURUH DATA DATABASE        ");
        $this->info(" Target Database: " . implode(', ', $databases));
        $this->info("==================================================");

        $defaultConn = config('database.default', 'mysql');
        $originalDb = Config::get("database.connections.{$defaultConn}.database");

        $tables = [
            'rpd',
            'rp',
            'sts',
            'acr',
            'itmdt',
            'itm',
            'sls',
            'gd',
            'ktg',
            'mrk',
            'spp',
            'cst',
            'kas',
            'users',
        ];

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

            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    $this->line(" - [{$db}] Tabel <comment>{$table}</comment> berhasil dikosongkan.");
                }
            }

            if (Schema::hasTable('ds')) {
                DB::table('ds')->truncate();
                $this->line(" - [{$db}] Tabel <comment>ds</comment> berhasil dikosongkan.");
            }

            // Buat 1 user admin default
            $this->info(" - [{$db}] Membuat 1 user admin default...");
            User::create([
                'USERNAME' => 'admin',
                'PASS'     => Hash::make('admin123'),
                'AL'       => 'Jl. Trunojoyo No. 45, Sumenep, Madura',
                'HP'       => '081234567890',
                'DES'      => 'Administrator Toko',
                'ROLE'     => 1,
            ]);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info(">>> Selesai mengosongkan [{$db}].");
        }

        // Kembalikan ke database asal
        Config::set("database.connections.{$defaultConn}.database", $originalDb);
        DB::purge($defaultConn);
        DB::reconnect($defaultConn);

        $this->newLine();
        $this->info("==================================================");
        $this->info(" DATABASE BERHASIL DIKOSONGKAN!");
        $this->info(" User Default: admin | Password: admin123");
        $this->info("==================================================");

        return Command::SUCCESS;
    }
}
