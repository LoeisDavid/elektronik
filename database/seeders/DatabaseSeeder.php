<?php

namespace Database\Seeders;

use App\Models\ACR;
use App\Models\CST;
use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\KTG;
use App\Models\MRK;
use App\Models\SLS;
use App\Models\SPP;
use App\Models\STS;
use App\Models\User;
use Database\Factories\ACRFactory;
use Database\Factories\CSTFactory;
use Database\Factories\GDFactory;
use Database\Factories\ITMDTFactory;
use Database\Factories\ITMFactory;
use Database\Factories\KTGFactory;
use Database\Factories\MRKFactory;
use Database\Factories\SLSFactory;
use Database\Factories\SPPFactory;
use Database\Factories\STSFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with 10,000 records for each migration.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = ['sts', 'acr', 'itmdt', 'itm', 'sls', 'gd', 'ktg', 'mrk', 'spp', 'cst', 'users'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        $totalRecords = 10000;
        $chunkSize = 1000;

        // 1. Users (admin + 10,000 users)
        $this->command?->info("Seeding Users (admin + 10,000 records)...");
        User::create([
            'USERNAME' => 'admin',
            'PASS' => Hash::make('admin123'),
            'AL' => 'Jl. Elektronik No. 1',
            'HP' => '08123456789',
            'DES' => 'Administrator Toko',
            'ROLE' => 1,
        ]);

        UserFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = User::factory()->make()->getAttributes();
            }
            DB::table('users')->insert($data);
        }

        // 2. CST (Customer) - 10,000
        $this->command?->info("Seeding CST (10,000 records)...");
        CSTFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = CST::factory()->make()->getAttributes();
            }
            DB::table('cst')->insert($data);
        }

        // 3. SPP (Supplier) - 10,000
        $this->command?->info("Seeding SPP (10,000 records)...");
        SPPFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = SPP::factory()->make()->getAttributes();
            }
            DB::table('spp')->insert($data);
        }

        // 4. MRK (Merek) - 10,000
        $this->command?->info("Seeding MRK (10,000 records)...");
        MRKFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = MRK::factory()->make()->getAttributes();
            }
            DB::table('mrk')->insert($data);
        }

        // 5. KTG (Kategori) - 10,000
        $this->command?->info("Seeding KTG (10,000 records)...");
        KTGFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = KTG::factory()->make()->getAttributes();
            }
            DB::table('ktg')->insert($data);
        }

        // 6. GD (Gudang) - 10,000
        $this->command?->info("Seeding GD (10,000 records)...");
        GDFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = GD::factory()->make()->getAttributes();
            }
            DB::table('gd')->insert($data);
        }

        // 7. SLS (Sales) - 10,000
        $this->command?->info("Seeding SLS (10,000 records)...");
        SLSFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = SLS::factory()->make()->getAttributes();
            }
            DB::table('sls')->insert($data);
        }

        // 8. ITM (Item) - 10,000
        $this->command?->info("Seeding ITM (10,000 records)...");
        ITMFactory::resetSequence(1);
        $globalIndex = 1;
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = ITM::factory()->make([
                    'KTG' => sprintf('KTG%07d', $globalIndex),
                    'MRK' => sprintf('MRK%07d', $globalIndex),
                    'SPP' => sprintf('SPP%07d', $globalIndex),
                ])->getAttributes();
                $globalIndex++;
            }
            DB::table('itm')->insert($data);
        }

        // 9. ITMDT (Item Detail) - 10,000
        $this->command?->info("Seeding ITMDT (10,000 records)...");
        ITMDTFactory::resetSequence(1);
        $globalIndex = 1;
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = ITMDT::factory()->make([
                    'ITM' => sprintf('ITM%07d', $globalIndex),
                    'GD'  => sprintf('GD%08d', $globalIndex),
                ])->getAttributes();
                $globalIndex++;
            }
            DB::table('itmdt')->insert($data);
        }

        // 10. ACR (Account Receivable / Header Penjualan) - 10,000
        $this->command?->info("Seeding ACR (10,000 records)...");
        ACRFactory::resetSequence(1);
        $globalIndex = 1;
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = ACR::factory()->make([
                    'CST'  => sprintf('CST%07d', $globalIndex),
                    'SLS'  => sprintf('SLS%07d', $globalIndex),
                    'USER' => sprintf('usr%07d', $globalIndex),
                ])->getAttributes();
                $globalIndex++;
            }
            DB::table('acr')->insert($data);
        }

        // 11. STS (Surat / Detail Penjualan) - Sisakan 10 item belum terjual (index 1-10)
        $this->command?->info("Seeding STS (9,990 records, 10 item belum terjual)...");
        STSFactory::resetSequence(0);
        $globalIndex = 11;
        $stsRecords = $totalRecords - 10;
        for ($offset = 0; $offset < $stsRecords; $offset += $chunkSize) {
            $limit = min($chunkSize, $stsRecords - $offset);
            $data = [];
            for ($i = 0; $i < $limit; $i++) {
                $data[] = STS::factory()->make([
                    'REF_NO' => sprintf('INV%07d', $globalIndex),
                    'BC_OUT' => sprintf('BCO%017d', $globalIndex),
                    'ITM'    => sprintf('ITM%07d', $globalIndex),
                ])->getAttributes();
                $globalIndex++;
            }
            DB::table('sts')->insert($data);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command?->info("All 11 migrations seeded with 10,000 records each successfully!");
    }
}
