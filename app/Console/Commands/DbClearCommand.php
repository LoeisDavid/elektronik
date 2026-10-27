<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
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
    protected $signature = 'db:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus seluruh data di database dan hanya menyisakan satu user admin default.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("==================================================");
        $this->info("       MENGOSONGKAN SELURUH DATA DATABASE        ");
        $this->info("==================================================");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

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

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->line(" - Tabel <comment>{$table}</comment> berhasil dikosongkan.");
            }
        }

        if (Schema::hasTable('ds')) {
            DB::table('ds')->truncate();
            $this->line(" - Tabel <comment>ds</comment> berhasil dikosongkan.");
        }

        // Buat 1 user admin default
        $this->info("\nMembuat 1 user admin default...");
        User::create([
            'USERNAME' => 'admin',
            'PASS'     => Hash::make('admin123'),
            'AL'       => 'Jl. Trunojoyo No. 45, Sumenep, Madura',
            'HP'       => '081234567890',
            'DES'      => 'Administrator Toko',
            'ROLE'     => 1,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("==================================================");
        $this->info(" DATABASE BERHASIL DIKOSONGKAN!");
        $this->info(" User Default: admin | Password: admin123");
        $this->info("==================================================");

        return Command::SUCCESS;
    }
}
