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
use App\Models\RP;
use App\Models\RPD;
use App\Models\STS;
use App\Models\User;
use Database\Factories\ACRFactory;
use Database\Factories\CSTFactory;
use Database\Factories\DSFactory;
use Database\Factories\GDFactory;
use Database\Factories\ITMDTFactory;
use Database\Factories\ITMFactory;
use Database\Factories\KASFactory;
use Database\Factories\KTGFactory;
use Database\Factories\MRKFactory;
use Database\Factories\RPDFactory;
use Database\Factories\RPFactory;
use Database\Factories\SLSFactory;
use Database\Factories\SPPFactory;
use Database\Factories\STSFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait SeedsStoreData
{
    /**
     * Helper seeding data master, produk, transaksi, dan shift kasir.
     */
    public function seedData(int $totalRecords, int $kasCount, bool $openCashierToday = false): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = ['rpd', 'rp', 'sts', 'acr', 'itmdt', 'itm', 'sls', 'gd', 'ktg', 'mrk', 'spp', 'cst', 'ds', 'users', 'kas'];
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        $chunkSize = min(500, max(50, $totalRecords));

        // 1. Users (1 user admin)
        $this->command?->info("Seeding Users (1 user admin: admin / admin123)...");
        User::create([
            'USERNAME' => 'admin',
            'PASS'     => Hash::make('admin123'),
            'AL'       => 'Jl. Raya Elektronik No. 88, Jakarta Pusat',
            'HP'       => '081234567890',
            'DES'      => 'Administrator Toko',
            'ROLE'     => 1,
        ]);

        // 2. CST (Customer) & DS (Desa)
        $this->command?->info("Seeding CST & DS ($totalRecords records)...");
        DSFactory::resetSequence(1);
        CSTFactory::resetSequence(1);
        $cstData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $cstData[] = CST::factory()->make()->getAttributes();
        }
        foreach (array_chunk($cstData, $chunkSize) as $chunk) {
            DB::table('cst')->insert($chunk);
        }

        // 3. SPP (Supplier)
        $this->command?->info("Seeding SPP ($totalRecords records)...");
        SPPFactory::resetSequence(1);
        $sppData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $sppData[] = SPP::factory()->make()->getAttributes();
        }
        foreach (array_chunk($sppData, $chunkSize) as $chunk) {
            DB::table('spp')->insert($chunk);
        }

        // 4. MRK (Merek)
        $this->command?->info("Seeding MRK ($totalRecords records)...");
        MRKFactory::resetSequence(1);
        $mrkData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $mrkData[] = MRK::factory()->make()->getAttributes();
        }
        foreach (array_chunk($mrkData, $chunkSize) as $chunk) {
            DB::table('mrk')->insert($chunk);
        }

        // 5. KTG (Kategori)
        $this->command?->info("Seeding KTG ($totalRecords records)...");
        KTGFactory::resetSequence(1);
        $ktgData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $ktgData[] = KTG::factory()->make()->getAttributes();
        }
        foreach (array_chunk($ktgData, $chunkSize) as $chunk) {
            DB::table('ktg')->insert($chunk);
        }

        // 6. GD (Gudang)
        $this->command?->info("Seeding GD ($totalRecords records)...");
        GDFactory::resetSequence(1);
        $gdData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $gdData[] = GD::factory()->make()->getAttributes();
        }
        foreach (array_chunk($gdData, $chunkSize) as $chunk) {
            DB::table('gd')->insert($chunk);
        }

        // 7. SLS (Sales)
        $this->command?->info("Seeding SLS ($totalRecords records)...");
        SLSFactory::resetSequence(1);
        $slsData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $slsData[] = SLS::factory()->make()->getAttributes();
        }
        foreach (array_chunk($slsData, $chunkSize) as $chunk) {
            DB::table('sls')->insert($chunk);
        }

        // 8. ITM, 9. ITMDT, 10. ACR & 11. STS
        $this->command?->info("Seeding ITM, ITMDT, ACR & STS ($totalRecords items)...");
        ITMFactory::resetSequence(1);
        ITMDTFactory::resetSequence(1);
        ACRFactory::resetSequence(1);
        STSFactory::resetSequence(0);

        $itmData = [];
        $itmdtData = [];
        $unitsToSell = [];
        $itmdtSequence = 1;

        for ($i = 1; $i <= $totalRecords; $i++) {
            $itmCode = sprintf('ITM%07d', $i);
            $ktgCode = sprintf('KTG%07d', (($i - 1) % $totalRecords) + 1);
            $mrkCode = sprintf('MRK%07d', (($i - 1) % $totalRecords) + 1);
            $sppCode = sprintf('SPP%07d', (($i - 1) % $totalRecords) + 1);

            $mod = $i % 10;
            if ($mod === 0 || $mod === 5) {
                // 1. STOK HABIS (~20%)
                if ($mod === 0) {
                    $soldUnits = (($i % 4) + 2);
                    $numItmdt  = $soldUnits;
                    $finalStok = 0;
                } else {
                    $numItmdt  = 0;
                    $soldUnits = 0;
                    $finalStok = 0;
                }
            } elseif ($mod === 1 || $mod === 3 || $mod === 7 || $mod === 9) {
                // 2. STOK MENIPIS (~40% -> STOK 1..5 unit)
                $finalStok = (($i % 5) + 1);
                $soldUnits = (($i % 4) + 1);
                $numItmdt  = $finalStok + $soldUnits;
            } else {
                // 3. STOK BANYAK (~40% -> STOK 6..20 unit)
                $finalStok = (($i % 15) + 6);
                $soldUnits = ($i % 6);
                $numItmdt  = $finalStok + $soldUnits;
            }

            $itmObj = ITM::factory()->make([
                'ITM'  => $itmCode,
                'STOK' => $finalStok,
                'KTG'  => $ktgCode,
                'MRK'  => $mrkCode,
            ])->getAttributes();
            $itmData[] = $itmObj;

            for ($s = 0; $s < $numItmdt; $s++) {
                $bcOut  = sprintf('BCO%017d', $itmdtSequence);
                $gdCode = sprintf('GD%08d', (($itmdtSequence - 1) % $totalRecords) + 1);

                $itmdtObj = ITMDT::factory()->make([
                    'ITM'    => $itmCode,
                    'BC_IN'  => sprintf('BCI%017d', $itmdtSequence),
                    'BC_OUT' => $bcOut,
                    'GD'     => $gdCode,
                    'SPP'    => $sppCode,
                ])->getAttributes();

                $itmdtData[] = $itmdtObj;

                if ($s < $soldUnits) {
                    $unitsToSell[] = [
                        'BC_OUT' => $bcOut,
                        'ITM'    => $itmCode,
                        'HRG'    => $itmObj['HRG'],
                    ];
                }

                $itmdtSequence++;
            }
        }

        foreach (array_chunk($itmData, $chunkSize) as $chunk) {
            DB::table('itm')->insert($chunk);
        }

        foreach (array_chunk($itmdtData, $chunkSize) as $chunk) {
            DB::table('itmdt')->insert($chunk);
        }

        // ACR & STS
        $totalSoldUnits = count($unitsToSell);
        $this->command?->info("Seeding ACR & STS ($totalSoldUnits unit terjual)...");

        $acrData = [];
        $stsData = [];
        $stsRecIndex = 0;
        $invIndex = 1;
        $cstIndex = 1;
        $slsIndex = 1;

        $soldOffset = 0;
        while ($soldOffset < $totalSoldUnits) {
            $itemsInInvoice = min(rand(1, 3), $totalSoldUnits - $soldOffset);
            $refNo = sprintf('INV%07d', $invIndex);
            $cstCode = sprintf('CST%07d', (($cstIndex - 1) % $totalRecords) + 1);
            $slsCode = sprintf('SLS%07d', (($slsIndex - 1) % $totalRecords) + 1);

            $brtTotal = 0;
            for ($j = 0; $j < $itemsInInvoice; $j++) {
                $soldUnit = $unitsToSell[$soldOffset + $j];
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

        // Verifikasi sinkronisasi stok: ITM.STOK == COUNT(ITMDT) - COUNT(STS)
        $mismatches = DB::select("
            SELECT itm.ITM, itm.STOK,
                   COALESCE(dt.total_dt, 0) as total_dt,
                   COALESCE(st.total_sts, 0) as total_sts
            FROM itm
            LEFT JOIN (SELECT ITM, COUNT(*) as total_dt FROM itmdt GROUP BY ITM) dt ON itm.ITM = dt.ITM
            LEFT JOIN (SELECT ITM, COUNT(*) as total_sts FROM sts GROUP BY ITM) st ON itm.ITM = st.ITM
            WHERE itm.STOK != (COALESCE(dt.total_dt, 0) - COALESCE(st.total_sts, 0))
        ");

        if (count($mismatches) > 0) {
            $this->command?->error("Peringatan: Terdapat " . count($mismatches) . " item dengan stok tidak sinkron!");
        } else {
            $this->command?->info("Verifikasi Sukses: 100% item memiliki STOK = jumlah ITMDT - jumlah STS.");
        }

        // 12. KAS (Riwayat shift kasir)
        $this->command?->info("Seeding KAS ($kasCount riwayat shift)...");
        KASFactory::resetSequence(1);
        KAS::factory()->count($kasCount)->create();

        if ($openCashierToday) {
            $todayStr = now()->toDateString();
            KAS::factory()->create([
                'TGL'  => $todayStr,
                'TGLM' => $todayStr . ' ' . sprintf('%02d:%02d:%02d', fake()->numberBetween(7, 10), fake()->numberBetween(0, 59), fake()->numberBetween(0, 59)),
                'TGLK' => null,
            ]);
        }

        // 13. RP & RPD (Perpindahan Gudang: 5-20 barang per dokumen)
        $this->command?->info("Seeding Perpindahan Gudang (RP & RPD)...");
        RPFactory::resetSequence(1);
        RPDFactory::resetSequence(1);

        $rpScenarios = [
            'request',
            'pengiriman',
            'pengiriman',
            'diterima',
            'ditolak',
        ];

        foreach ($rpScenarios as $state) {
            $itemCount = rand(5, 20);
            RP::factory()->$state()->withItems($itemCount)->create();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
