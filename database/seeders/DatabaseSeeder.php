<?php

namespace Database\Seeders;

use App\Models\ACR;
use App\Models\CST;
use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\KAS;
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
use Database\Factories\KASFactory;
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
     * Seed the application's database with electronic items, matched stock, single user, and unsold stock.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = ['sts', 'acr', 'itmdt', 'itm', 'sls', 'gd', 'ktg', 'mrk', 'spp', 'cst', 'users', 'kas'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        $totalRecords = 1000;
        $chunkSize = 500;

        // 1. Users (Hanya 1 user admin)
        $this->command?->info("Seeding Users (1 user admin)...");
        User::create([
            'USERNAME' => 'admin',
            'PASS'     => Hash::make('admin123'),
            'AL'       => 'Jl. Raya Elektronik No. 88, Jakarta Pusat',
            'HP'       => '081234567890',
            'DES'      => 'Administrator Toko',
            'ROLE'     => 1,
        ]);

        // 2. CST (Customer)
        $this->command?->info("Seeding CST ($totalRecords records)...");
        CSTFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = CST::factory()->make()->getAttributes();
            }
            DB::table('cst')->insert($data);
        }

        // 3. SPP (Supplier)
        $this->command?->info("Seeding SPP ($totalRecords records)...");
        SPPFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = SPP::factory()->make()->getAttributes();
            }
            DB::table('spp')->insert($data);
        }

        // 4. MRK (Merek)
        $this->command?->info("Seeding MRK ($totalRecords records)...");
        MRKFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = MRK::factory()->make()->getAttributes();
            }
            DB::table('mrk')->insert($data);
        }

        // 5. KTG (Kategori)
        $this->command?->info("Seeding KTG ($totalRecords records)...");
        KTGFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = KTG::factory()->make()->getAttributes();
            }
            DB::table('ktg')->insert($data);
        }

        // 6. GD (Gudang)
        $this->command?->info("Seeding GD ($totalRecords records)...");
        GDFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = GD::factory()->make()->getAttributes();
            }
            DB::table('gd')->insert($data);
        }

        // 7. SLS (Sales)
        $this->command?->info("Seeding SLS ($totalRecords records)...");
        SLSFactory::resetSequence(1);
        for ($chunk = 0; $chunk < ($totalRecords / $chunkSize); $chunk++) {
            $data = [];
            for ($i = 0; $i < $chunkSize; $i++) {
                $data[] = SLS::factory()->make()->getAttributes();
            }
            DB::table('sls')->insert($data);
        }

        // 8. ITM (Item) & 9. ITMDT (Item Detail)
        // Stok pada ITM disesuaikan dengan jumlah ITMDT. Beberapa ITM tidak memiliki stok (STOK = 0).
        $this->command?->info("Seeding ITM & ITMDT (stok ITM sesuai jumlah ITMDT)...");
        ITMFactory::resetSequence(1);
        ITMDTFactory::resetSequence(1);

        $itmData = [];
        $itmdtData = [];
        $itmdtSequence = 1;
        $allCreatedItmdts = [];

        for ($i = 1; $i <= $totalRecords; $i++) {
            $itmCode = sprintf('ITM%07d', $i);
            $ktgCode = sprintf('KTG%07d', (($i - 1) % $totalRecords) + 1);
            $mrkCode = sprintf('MRK%07d', (($i - 1) % $totalRecords) + 1);
            $sppCode = sprintf('SPP%07d', (($i - 1) % $totalRecords) + 1);

            // ~15% barang tidak ada stok (STOK = 0), sisa barang punya stok 1 s/d 6 unit
            if ($i % 7 === 0) {
                $stockCount = 0;
            } else {
                $stockCount = (($i * 3 + 7) % 6) + 1;
            }

            $itmObj = ITM::factory()->make([
                'ITM'  => $itmCode,
                'STOK' => $stockCount,
                'KTG'  => $ktgCode,
                'MRK'  => $mrkCode,
                'SPP'  => $sppCode,
            ])->getAttributes();
            $itmData[] = $itmObj;

            for ($s = 0; $s < $stockCount; $s++) {
                $bcOut = sprintf('BCO%017d', $itmdtSequence);
                $gdCode = sprintf('GD%08d', (($itmdtSequence - 1) % $totalRecords) + 1);

                $itmdtObj = ITMDT::factory()->make([
                    'ITM'    => $itmCode,
                    'BC_IN'  => sprintf('BCI%017d', $itmdtSequence),
                    'BC_OUT' => $bcOut,
                    'GD'     => $gdCode,
                ])->getAttributes();

                $itmdtData[] = $itmdtObj;
                $allCreatedItmdts[] = [
                    'BC_OUT' => $bcOut,
                    'ITM'    => $itmCode,
                    'HRG'    => $itmObj['HRG'],
                ];
                $itmdtSequence++;
            }
        }

        foreach (array_chunk($itmData, $chunkSize) as $chunk) {
            DB::table('itm')->insert($chunk);
        }

        foreach (array_chunk($itmdtData, $chunkSize) as $chunk) {
            DB::table('itmdt')->insert($chunk);
        }

        // 10. ACR & 11. STS (Penjualan)
        // Hanya sebagian stok (sekitar 60%) yang terjual di STS, sisanya 40% belum terjual
        $totalItmdts = count($allCreatedItmdts);
        $soldCount = (int) ($totalItmdts * 0.6);
        $soldItmdts = array_slice($allCreatedItmdts, 0, $soldCount);

        $this->command?->info("Seeding ACR & STS ($soldCount unit terjual dari total $totalItmdts unit ITMDT)...");

        ACRFactory::resetSequence(1);
        STSFactory::resetSequence(0);

        $acrData = [];
        $stsData = [];
        $stsRecIndex = 0;
        $invIndex = 1;
        $cstIndex = 1;
        $slsIndex = 1;

        $soldOffset = 0;
        while ($soldOffset < $soldCount) {
            $itemsInInvoice = min(rand(1, 3), $soldCount - $soldOffset);
            $refNo = sprintf('INV%07d', $invIndex);
            $cstCode = sprintf('CST%07d', (($cstIndex - 1) % $totalRecords) + 1);
            $slsCode = sprintf('SLS%07d', (($slsIndex - 1) % $totalRecords) + 1);

            $brtTotal = 0;
            for ($j = 0; $j < $itemsInInvoice; $j++) {
                $soldUnit = $soldItmdts[$soldOffset + $j];
                $recKey = str_pad(base_convert((string)$stsRecIndex, 10, 36), 3, '0', STR_PAD_LEFT);
                $hgj = $soldUnit['HRG'];
                $brtTotal += $hgj;

                $stsData[] = [
                    'REC'        => $recKey,
                    'REF_NO'     => $refNo,
                    'BC_OUT'     => $soldUnit['BC_OUT'],
                    'ITM'        => $soldUnit['ITM'],
                    'HGJ'        => $hgj,
                    'ST'         => 'UNT',
                    'POT_1'      => null,
                    'POT_2'      => null,
                    'POT_R'      => null,
                    'PPN'        => null,
                    'BRT'        => $hgj,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $stsRecIndex++;
            }

            $acrData[] = [
                'REF_NO'     => $refNo,
                'DT'         => fake()->date(),
                'CST'        => $cstCode,
                'SLS'        => $slsCode,
                'BRT'        => $brtTotal,
                'PAY'        => $brtTotal,
                'TOT_POT'    => 0,
                'JNS'        => 1,
                'POT_1'      => null,
                'POT_2'      => null,
                'POT_R'      => null,
                'USER'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $soldOffset += $itemsInInvoice;
            $invIndex++;
            $cstIndex++;
            $slsIndex++;
        }

        foreach (array_chunk($acrData, $chunkSize) as $chunk) {
            DB::table('acr')->insert($chunk);
        }
        foreach (array_chunk($stsData, $chunkSize) as $chunk) {
            DB::table('sts')->insert($chunk);
        }

        // 12. KAS (Memanggil KASFactory untuk seed riwayat shift kasir)
        $this->command?->info("Seeding KAS (menggunakan KASFactory)...");
        KASFactory::resetSequence(1);
        KAS::factory()->count(60)->create();
        // Pastikan ada KAS record untuk hari ini juga
        $todayStr = now()->toDateString();
        if (!KAS::where('TGL', $todayStr)->exists()) {
            KAS::factory()->create([
                'TGL'  => $todayStr,
                'TGLM' => $todayStr . ' ' . sprintf('%02d:%02d:%02d', fake()->numberBetween(7, 10), fake()->numberBetween(0, 59), fake()->numberBetween(0, 59)),
                'TGLK' => null,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command?->info("Seeding database berhasil!");
    }
}

