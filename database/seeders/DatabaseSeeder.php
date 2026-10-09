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

        // 8. ITM (Item), 9. ITMDT (Item Detail), 10. ACR & 11. STS (Penjualan)
        // Aturan: STOK pada ITM HARUS persis sama dengan jumlah ITMDT dikurangi jumlah STS (ITMDT - STS).
        // Distribusi stok barang elektronik:
        // - Habis (STOK = 0): ~20% barang (ada yang ludes terjual di STS, ada yang belum ada stok)
        // - Menipis (STOK = 1..5): ~40% barang (sesuai ambang BATAS_MENIPIS di aplikasi)
        // - Banyak (STOK = 6..20): ~40% barang (stok aman dan melimpah)
        $this->command?->info("Seeding ITM, ITMDT, ACR & STS (stok ITM = jumlah ITMDT - jumlah STS)...");
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
                // 1. STOK HABIS (200 item / 20%)
                if ($mod === 0) {
                    // Pernah ada stok masuk, tetapi seluruh unitnya ludes terjual di STS
                    $soldUnits = (($i % 4) + 2); // 2..5 unit masuk & terjual
                    $numItmdt  = $soldUnits;
                    $finalStok = 0;
                } else {
                    // Belum pernah ada stok masuk
                    $numItmdt  = 0;
                    $soldUnits = 0;
                    $finalStok = 0;
                }
            } elseif ($mod === 1 || $mod === 3 || $mod === 7 || $mod === 9) {
                // 2. STOK MENIPIS (400 item / 40% -> STOK 1..5 unit)
                $finalStok = (($i % 5) + 1); // 1, 2, 3, 4, atau 5 unit
                $soldUnits = (($i % 4) + 1); // 1, 2, 3, atau 4 unit terjual di STS
                $numItmdt  = $finalStok + $soldUnits;
            } else {
                // 3. STOK BANYAK (400 item / 40% -> STOK 6..20 unit)
                $finalStok = (($i % 15) + 6); // 6 s/d 20 unit
                $soldUnits = ($i % 6);        // 0 s/d 5 unit terjual di STS
                $numItmdt  = $finalStok + $soldUnits;
            }

            $itmObj = ITM::factory()->make([
                'ITM'  => $itmCode,
                'STOK' => $finalStok, // Tepat jumlah ITMDT dikurangi jumlah STS
                'KTG'  => $ktgCode,
                'MRK'  => $mrkCode,
                'SPP'  => $sppCode,
            ])->getAttributes();
            $itmData[] = $itmObj;

            // Generate rincian unit masuk di ITMDT
            for ($s = 0; $s < $numItmdt; $s++) {
                $bcOut  = sprintf('BCO%017d', $itmdtSequence);
                $gdCode = sprintf('GD%08d', (($itmdtSequence - 1) % $totalRecords) + 1);

                $itmdtObj = ITMDT::factory()->make([
                    'ITM'    => $itmCode,
                    'BC_IN'  => sprintf('BCI%017d', $itmdtSequence),
                    'BC_OUT' => $bcOut,
                    'GD'     => $gdCode,
                ])->getAttributes();

                $itmdtData[] = $itmdtObj;

                // Daftarkan unit yang terjual di STS
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

        // 10. ACR & 11. STS (Penjualan / Invoice)
        $totalSoldUnits = count($unitsToSell);
        $this->command?->info("Seeding ACR & STS ($totalSoldUnits unit terjual dari ITMDT)...");

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

        // Verifikasi konsistensi stok: ITM.STOK == COUNT(ITMDT) - COUNT(STS)
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

        // 12. KAS (Memanggil KASFactory untuk seed riwayat shift kasir)
        $this->command?->info("Seeding KAS (menggunakan KASFactory)...");
        KASFactory::resetSequence(1);
        KAS::factory()->count(60)->create();
        // Pastikan ada KAS record untuk hari ini juga
        $todayStr = now()->toDateString();
        if (!KAS::where('TGL', $todayStr)->exists()) {
            KAS::factory()->create([
                'TGL'  => $todayStr,
                'TGLM' => $todayStr . ' 08:00:00',
                'TGLK' => $todayStr . ' 17:00:00',
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command?->info("Seeding database berhasil!");
    }
}

