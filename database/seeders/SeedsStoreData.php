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
use Illuminate\Support\Facades\Schema;

trait SeedsStoreData
{
    /**
     * Daftar katalog produk Elektronik (DB 1) dengan harga beli (hrb) & harga jual (hrg) terjangkau untuk presentasi.
     */
    protected function getCatalogElektronik(): array
    {
        return [
            ['des' => 'Kipas Angin Stand Fan 16 Inch',     'hrb' => 160000, 'hrg' => 210000],
            ['des' => 'Setrika Listrik Anti Lengket',      'hrb' => 85000,  'hrg' => 115000],
            ['des' => 'Rice Cooker Digital 1.8L',          'hrb' => 220000, 'hrg' => 285000],
            ['des' => 'Blender Multi-Function 2L',         'hrb' => 175000, 'hrg' => 230000],
            ['des' => 'Set Top Box TV Digital DVB-T2',     'hrb' => 120000, 'hrg' => 165000],
            ['des' => 'Speaker Bluetooth Portable',        'hrb' => 95000,  'hrg' => 135000],
            ['des' => 'Teko Listrik Electric Kettle 1.5L', 'hrb' => 75000,  'hrg' => 105000],
            ['des' => 'Dispenser Air Meja Panas Dingin',   'hrb' => 140000, 'hrg' => 185000],
            ['des' => 'Kipas Angin Dinding 16 Inch',       'hrb' => 145000, 'hrg' => 190000],
            ['des' => 'Kompor Induksi Portabel Low Watt',  'hrb' => 260000, 'hrg' => 335000],
            ['des' => 'Air Fryer Digital 3.5L',            'hrb' => 320000, 'hrg' => 410000],
            ['des' => 'Chopper Penggiling Bumbu & Daging', 'hrb' => 110000, 'hrg' => 150000],
            ['des' => 'Toaster Pemanggang Roti 2 Slice',   'hrb' => 130000, 'hrg' => 175000],
            ['des' => 'Exhaust Fan Dinding 10 Inch',       'hrb' => 150000, 'hrg' => 195000],
            ['des' => 'Mixer Adonan Kue Stand Mixer',      'hrb' => 185000, 'hrg' => 245000],
            ['des' => 'Kamera CCTV WiFi Smart 1080p',      'hrb' => 165000, 'hrg' => 220000],
            ['des' => 'Router Wi-Fi Dual Band AC1200',     'hrb' => 155000, 'hrg' => 205000],
            ['des' => 'Powerbank Fast Charging 10000mAh',  'hrb' => 90000,  'hrg' => 125000],
            ['des' => 'Stabilizer Voltase Otomatis 500VA', 'hrb' => 195000, 'hrg' => 255000],
            ['des' => 'Rice Cooker Mini Travel 0.8L',      'hrb' => 135000, 'hrg' => 180000],
        ];
    }

    /**
     * Daftar katalog produk Sparepart Elektronik (DB 2) dengan harga beli (hrb) & harga jual (hrg) terjangkau.
     */
    protected function getCatalogSparepart(): array
    {
        return [
            ['des' => 'Kapasitor Kipas Angin 1.5uF 450V',      'hrb' => 8000,  'hrg' => 15000],
            ['des' => 'Thermofuse Rice Cooker 157 Derajat',    'hrb' => 5000,  'hrg' => 10000],
            ['des' => 'Elemen Pemanas Setrika Listrik 350W',   'hrb' => 18000, 'hrg' => 28000],
            ['des' => 'Gearbox Mesin Cuci 2 Tabung',           'hrb' => 45000, 'hrg' => 65000],
            ['des' => 'Dinamo Motor Kipas Angin Universal',    'hrb' => 55000, 'hrg' => 80000],
            ['des' => 'Seal Karet Pembuangan Mesin Cuci',      'hrb' => 12000, 'hrg' => 20000],
            ['des' => 'Baling-Baling Kipas Angin 16 Inch',     'hrb' => 22000, 'hrg' => 35000],
            ['des' => 'Pisau Blender Mounting Set Lengkap',    'hrb' => 25000, 'hrg' => 40000],
            ['des' => 'Kabel Power Angka 8 Panjang 1.5m',      'hrb' => 10000, 'hrg' => 18000],
            ['des' => 'Kapasitor Mesin Cuci 10uF + 4uF',       'hrb' => 24000, 'hrg' => 38000],
            ['des' => 'Timer Pencuci Mesin Cuci 6 Kabel',      'hrb' => 28000, 'hrg' => 45000],
            ['des' => 'Saklar Switch Kipas Angin 4 Pin',       'hrb' => 12000, 'hrg' => 20000],
            ['des' => 'Thermostat Otomatis Setrika & MagicCom','hrb' => 15000, 'hrg' => 25000],
            ['des' => 'Konektor Jack Antena TV Coaxial Besi',  'hrb' => 6000,  'hrg' => 12000],
            ['des' => 'Fuse Sekring Kaca 5A 250V (Pack)',      'hrb' => 8000,  'hrg' => 15000],
            ['des' => 'Solder Listrik 40W Mata Lancip',        'hrb' => 32000, 'hrg' => 48000],
            ['des' => 'Timah Solder Roll 60/40 0.8mm',         'hrb' => 20000, 'hrg' => 32000],
            ['des' => 'Multitester Digital Avometer DT830B',   'hrb' => 42000, 'hrg' => 65000],
            ['des' => 'IC Regulator Tegangan L7805 CV',        'hrb' => 7000,  'hrg' => 14000],
            ['des' => 'Dinamo Spin Pengering Mesin Cuci 70W',  'hrb' => 95000, 'hrg' => 135000],
        ];
    }

    /**
     * Helper seeding data master produk elektronik / sparepart, customer, desa, transaksi kasir, dan perpindahan gudang.
     */
    public function seedData(int $totalRecords, int $kasCount, bool $openCashierToday = false, string $mode = 'elektronik'): void
    {
        $isSparepart = (strtolower($mode) === 'sparepart');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = ['rpd', 'rp', 'sts', 'acr', 'itmdt', 'itm', 'sls', 'gd', 'ktg', 'mrk', 'spp', 'cst', 'users', 'kas'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }
        if (Schema::hasTable('ds')) {
            DB::table('ds')->truncate();
        }

        $chunkSize = min(500, max(50, $totalRecords));

        // 1. Users (manager, admin, storage, kasir)
        $this->command?->info("1. Seeding Users (manager, admin, storage, kasir)...");
        $usersToSeed = [
            [
                'USERNAME' => 'manager',
                'PASS'     => Hash::make('manager123'),
                'AL'       => 'Jl. Trunojoyo No. 45, Sumenep, Madura',
                'HP'       => '081234567891',
                'DES'      => $isSparepart ? 'Manager Sparepart Elektronik' : 'Manager Toko Elektronik',
                'ROLE'     => 4, // Manager
            ],
            [
                'USERNAME' => 'admin',
                'PASS'     => Hash::make('admin123'),
                'AL'       => 'Jl. Trunojoyo No. 45, Sumenep, Madura',
                'HP'       => '081234567890',
                'DES'      => $isSparepart ? 'Admin Sparepart Elektronik' : 'Administrator Toko Elektronik',
                'ROLE'     => 1, // Admin
            ],
            [
                'USERNAME' => 'storage',
                'PASS'     => Hash::make('storage123'),
                'AL'       => 'Gudang Utama Jakarta',
                'HP'       => '081234567892',
                'DES'      => $isSparepart ? 'Petugas Gudang Sparepart' : 'Petugas Gudang Elektronik',
                'ROLE'     => 3, // Storage
            ],
            [
                'USERNAME' => 'kasir',
                'PASS'     => Hash::make('kasir123'),
                'AL'       => 'Jl. Diponegoro No. 10, Sumenep',
                'HP'       => '081234567893',
                'DES'      => $isSparepart ? 'Kasir Sparepart Toko' : 'Kasir Toko Elektronik',
                'ROLE'     => 2, // Kasir
            ],
        ];

        foreach ($usersToSeed as $u) {
            User::create($u);
        }

        // 2. CST & Desa (di tabel cst: desa memiliki DS=null, customer memiliki DS=kode_desa)
        $this->command?->info("2. Seeding Desa & Customer di tabel CST ($totalRecords records)...");
        $desas = [
            'Kolor', 'Pabian', 'Bangselok', 'Pamolokan', 'Marengan Daya',
            'Marengan Laok', 'Kalianget Timur', 'Kalianget Barat', 'Guluk-Guluk', 'Saronggi',
            'Batuan', 'Lenteng Barat', 'Ambunten Timur', 'Gapura Tengah', 'Bluto',
            'Manding Laok', 'Dungkek', 'Talango', 'Pasongsongan', 'Rubaru',
        ];
        $desaCount = min(count($desas), max(10, (int)($totalRecords / 2)));
        $desaData = [];
        for ($d = 1; $d <= $desaCount; $d++) {
            $desaData[] = [
                'CST'        => sprintf('DS%07d', $d),
                'DES'        => $desas[($d - 1) % count($desas)],
                'AL'         => null,
                'TLP'        => null,
                'DS'         => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('cst')->insert($desaData);

        // Seeding Customer
        CSTFactory::resetSequence(1);
        $cstData = [];
        for ($i = 1; $i <= $totalRecords; $i++) {
            $desaRef = sprintf('DS%07d', (($i - 1) % $desaCount) + 1);
            $cstObj = CST::factory()->make(['DS' => $desaRef])->getAttributes();
            $cstData[] = $cstObj;
        }
        foreach (array_chunk($cstData, $chunkSize) as $chunk) {
            DB::table('cst')->insert($chunk);
        }

        // 3. SPP (Supplier)
        $labelMode = $isSparepart ? 'Sparepart Elektronik' : 'Elektronik';
        $this->command?->info("3. Seeding Supplier {$labelMode} ($totalRecords records)...");
        $sparepartSuppliers = [
            'CV Jaya Sparepart Elektronik',
            'PT Indo Komponen Utama',
            'UD Sumber Teknik Part',
            'CV Sentral Dinamo & Part',
            'PT Multi Suku Cadang',
            'UD Makmur Jaya Komponen',
            'CV Mitra Servis Elektronik',
            'PT Global Part Nusantara',
            'UD Sinar Teknik Sparepart',
            'CV Bintang Komponen',
        ];
        SPPFactory::resetSequence(1);
        $sppData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $attr = SPP::factory()->make()->getAttributes();
            if ($isSparepart) {
                $baseName = $sparepartSuppliers[$i % count($sparepartSuppliers)];
                if ($i >= count($sparepartSuppliers)) {
                    $baseName .= ' ' . (intval($i / count($sparepartSuppliers)) + 1);
                }
                $attr['DES'] = substr($baseName, 0, 150);
            }
            $sppData[] = $attr;
        }
        foreach (array_chunk($sppData, $chunkSize) as $chunk) {
            DB::table('spp')->insert($chunk);
        }

        // 4. MRK (Merk)
        $this->command?->info("4. Seeding Merk {$labelMode} ($totalRecords records)...");
        $sparepartBrands = [
            'San-Ei', 'Matsugi', 'Dekko', 'Nikkai', 'Visero',
            'Toyosaki', 'Goot', 'Omron', 'Miyako Part', 'Cosmos Part',
            'Panasonic Part', 'Sharp Part', 'Polytron Part', 'Maspion Part', 'Sanken Part',
        ];
        MRKFactory::resetSequence(1);
        $mrkData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $attr = MRK::factory()->make()->getAttributes();
            if ($isSparepart) {
                $baseBrand = $sparepartBrands[$i % count($sparepartBrands)];
                if ($i >= count($sparepartBrands)) {
                    $baseBrand .= ' ' . (intval($i / count($sparepartBrands)) + 1);
                }
                $attr['DES'] = substr($baseBrand, 0, 45);
            }
            $mrkData[] = $attr;
        }
        foreach (array_chunk($mrkData, $chunkSize) as $chunk) {
            DB::table('mrk')->insert($chunk);
        }

        // 5. KTG (Kategori)
        $this->command?->info("5. Seeding Kategori {$labelMode} ($totalRecords records)...");
        $sparepartCategories = [
            'Suku Cadang Kipas Angin',
            'Suku Cadang Mesin Cuci',
            'Suku Cadang Rice Cooker',
            'Suku Cadang Blender',
            'Suku Cadang Setrika',
            'Kapasitor & Komponen',
            'Kabel & Konektor',
            'Alat Servis & Solder',
            'IC & Semikonduktor',
            'Saklar & Thermostat',
            'Dinamo & Motor Listrik',
            'Fuse & Proteksi Arus',
            'Aksesoris Antena & TV',
            'Elemen Pemanas',
            'Modul & PCB Elektronik',
        ];
        KTGFactory::resetSequence(1);
        $ktgData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $attr = KTG::factory()->make()->getAttributes();
            if ($isSparepart) {
                $baseCat = $sparepartCategories[$i % count($sparepartCategories)];
                if ($i >= count($sparepartCategories)) {
                    $baseCat .= ' ' . (intval($i / count($sparepartCategories)) + 1);
                }
                $attr['DES'] = substr($baseCat, 0, 45);
            }
            $ktgData[] = $attr;
        }
        foreach (array_chunk($ktgData, $chunkSize) as $chunk) {
            DB::table('ktg')->insert($chunk);
        }

        // 6. GD (Gudang)
        $this->command?->info("6. Seeding Gudang ($totalRecords records)...");
        GDFactory::resetSequence(1);
        $gdData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $gdData[] = GD::factory()->make()->getAttributes();
        }
        foreach (array_chunk($gdData, $chunkSize) as $chunk) {
            DB::table('gd')->insert($chunk);
        }

        // 7. SLS (Salesperson)
        $this->command?->info("7. Seeding Salesperson ($totalRecords records)...");
        SLSFactory::resetSequence(1);
        $slsData = [];
        for ($i = 0; $i < $totalRecords; $i++) {
            $slsData[] = SLS::factory()->make()->getAttributes();
        }
        foreach (array_chunk($slsData, $chunkSize) as $chunk) {
            DB::table('sls')->insert($chunk);
        }

        // 8. ITM (Barang) & 9. ITMDT (Detail Unit & Barcode)
        $this->command?->info("8. Seeding Barang {$labelMode} & Barcode ITMDT ($totalRecords items)...");
        ITMFactory::resetSequence(1);
        ITMDTFactory::resetSequence(1);
        ACRFactory::resetSequence(1);
        STSFactory::resetSequence(0);

        $catalog = $isSparepart ? $this->getCatalogSparepart() : $this->getCatalogElektronik();

        $itmData = [];
        $itmdtData = [];
        $unitsToSell = [];
        $itmdtSequence = 1;

        for ($i = 1; $i <= $totalRecords; $i++) {
            $itmCode = sprintf('ITM%07d', $i);
            $ktgCode = sprintf('KTG%07d', (($i - 1) % $totalRecords) + 1);
            $mrkCode = sprintf('MRK%07d', (($i - 1) % $totalRecords) + 1);
            $sppCode = sprintf('SPP%07d', (($i - 1) % $totalRecords) + 1);

            $catItem = $catalog[($i - 1) % count($catalog)];
            $prodName = $catItem['des'];
            if ($i > count($catalog)) {
                $prodName .= ' Seri ' . sprintf('%03d', $i);
            }
            $hargaBeli = (int) $catItem['hrb'];
            $hargaJual = (int) $catItem['hrg'];

            $mod = $i % 10;
            if ($mod === 0 || $mod === 5) {
                // STOK HABIS (~20%)
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
                // STOK MENIPIS (~40% -> STOK 1..5 unit)
                $finalStok = (($i % 5) + 1);
                $soldUnits = (($i % 4) + 1);
                $numItmdt  = $finalStok + $soldUnits;
            } else {
                // STOK BANYAK (~40% -> STOK 6..15 unit)
                $finalStok = (($i % 10) + 6);
                $soldUnits = ($i % 5);
                $numItmdt  = $finalStok + $soldUnits;
            }

            $itmObj = ITM::factory()->make([
                'ITM'  => $itmCode,
                'DES'  => substr($prodName, 0, 150),
                'STOK' => $finalStok,
                'HRG'  => $hargaJual,
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
                    'HRB'    => $hargaBeli,
                    'GD'     => $gdCode,
                    'SPP'    => $sppCode,
                ])->getAttributes();

                $itmdtData[] = $itmdtObj;

                if ($s < $soldUnits) {
                    $unitsToSell[] = [
                        'BC_OUT' => $bcOut,
                        'ITM'    => $itmCode,
                        'HRG'    => $hargaJual,
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

        // 10. ACR & 11. STS (Riwayat Transaksi Penjualan)
        $totalSoldUnits = count($unitsToSell);
        $this->command?->info("9. Seeding Transaksi Kasir ACR & STS ($totalSoldUnits unit terjual)...");

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
                'DT'         => fake()->dateTimeBetween('-14 days', 'now')->format('Y-m-d'),
                'CST'        => $cstCode,
                'SLS'        => $slsCode,
                'BRT'        => $brtTotal,
                'PAY'        => $brtTotal,
                'TOT_POT'    => 0,
                'JNS'        => ($invIndex % 3 === 0) ? 2 : 1,
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
            $this->command?->warn("Peringatan: Terdapat " . count($mismatches) . " item dengan stok tidak sinkron!");
        } else {
            $this->command?->info("Verifikasi Stok: 100% item memiliki STOK = jumlah ITMDT - jumlah STS.");
        }

        // 12. KAS (Riwayat shift kasir)
        $this->command?->info("10. Seeding KAS ($kasCount riwayat shift)...");
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

        // 13. RP & RPD (Perpindahan Gudang: Request, Pengiriman, Diterima)
        $this->command?->info("11. Seeding Perpindahan Gudang (RP & RPD)...");
        RPFactory::resetSequence(1);
        RPDFactory::resetSequence(1);

        $rpScenarios = [
            'request',
            'pengiriman',
            'diterima',
        ];

        // Buat perpindahan barang
        $rpLoop = max(1, (int)($totalRecords / 6));
        for ($r = 0; $r < $rpLoop; $r++) {
            foreach ($rpScenarios as $state) {
                $itemCount = rand(3, min(10, max(3, (int)($totalRecords / 2))));
                try {
                    RP::factory()->$state()->withItems($itemCount)->create();
                } catch (\Exception $e) {
                    // Fallback jika tidak ada item tersedia
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
