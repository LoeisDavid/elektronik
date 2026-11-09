<?php

namespace Tests\Feature;

use App\Models\ACR;
use App\Models\CST;
use App\Models\DS;
use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\KAS;
use App\Models\KTG;
use App\Models\MRK;
use App\Models\RP;
use App\Models\RPD;
use App\Models\SLS;
use App\Models\SPP;
use App\Models\STS;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ComprehensiveE2EFlowTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Siapkan user admin untuk pengujian
        $this->user = User::firstOrCreate(
            ['USERNAME' => 'test_admin'],
            [
                'PASS' => Hash::make('password123'),
                'ROLE' => 1,
            ]
        );
    }

    /**
     * 1. Test Tambah dan Update Customer & Supplier
     */
    public function test_customer_and_supplier_crud_flow(): void
    {
        // Setup Desa pendukung (desa disimpan di cst dengan DS null)
        $desa = CST::firstOrCreate(
            ['CST' => 'DS_TEST01'],
            ['DES' => 'Desa Sukamaju Test', 'AL' => null, 'TLP' => null, 'DS' => null]
        );

        // 1A. Tambah Customer
        $customerData = [
            'DES' => 'Budi Santoso ' . rand(1000, 9999),
            'TLP' => '0812' . rand(10000000, 99999999),
            'AL'  => 'Jl. Merdeka No. 45',
            'DS'  => $desa->CST,
        ];

        $responseCst = $this->actingAs($this->user)->post(route('POS.customer.store'), $customerData);
        $responseCst->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cst', [
            'DES' => $customerData['DES'],
            'TLP' => $customerData['TLP'],
        ]);

        $cst = CST::where('DES', $customerData['DES'])->first();
        $this->assertNotNull($cst);

        // 1B. Update Customer
        $updateData = [
            'CST' => $cst->CST,
            'DES' => 'Budi Santoso Updated ' . rand(1000, 9999),
            'TLP' => '0813' . rand(10000000, 99999999),
            'AL'  => 'Jl. Sudirman No. 10',
            'DS'  => $desa->CST,
        ];
        $responseCstUpdate = $this->actingAs($this->user)->put(route('POS.customer.update', $cst->CST), $updateData);
        $responseCstUpdate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('cst', [
            'CST' => $cst->CST,
            'DES' => $updateData['DES'],
        ]);

        // 1C. Tambah Supplier
        $supplierData = [
            'DES' => 'PT Sumber Elektronik ' . rand(1000, 9999),
            'TLP' => '0821' . rand(10000000, 99999999),
        ];

        $responseSpp = $this->actingAs($this->user)->post(route('inventory.supplier.store'), $supplierData);
        $responseSpp->assertSessionHasNoErrors();
        $this->assertDatabaseHas('spp', [
            'DES' => $supplierData['DES'],
            'TLP' => $supplierData['TLP'],
        ]);

        $spp = SPP::where('DES', $supplierData['DES'])->first();
        $this->assertNotNull($spp);

        // 1D. Update Supplier
        $updateSppData = [
            'SPP' => $spp->SPP,
            'DES' => 'PT Sumber Elektronik Perkasa ' . rand(1000, 9999),
            'TLP' => '0822' . rand(10000000, 99999999),
        ];
        $responseSppUpdate = $this->actingAs($this->user)->put(route('inventory.supplier.update', $spp->SPP), $updateSppData);
        $responseSppUpdate->assertSessionHasNoErrors();
        $this->assertDatabaseHas('spp', [
            'SPP' => $spp->SPP,
            'DES' => $updateSppData['DES'],
        ]);
    }

    /**
     * 2. Test Tambah Barang (Produk) dan Tambah Stok
     */
    public function test_barang_dan_tambah_stok_flow(): void
    {
        // Pastikan Kategori, Merk, Supplier, dan Gudang ada
        $ktg = KTG::firstOrCreate(['KTG' => 'KTG_T1'], ['DES' => 'Elektronik Test']);
        $mrk = MRK::firstOrCreate(['MRK' => 'MRK_T1'], ['DES' => 'Sony Test']);
        $spp = SPP::firstOrCreate(['SPP' => 'SPP_T1'], ['DES' => 'Supplier Test', 'TLP' => '0811111111']);
        $gd  = GD::firstOrCreate(['GD' => 'GD_T1'], ['DES' => 'Gudang Utama Test']);

        // 2A. Tambah Barang Baru
        $itemData = [
            'DES' => 'Headphone Bluetooth ' . rand(1000, 9999),
            'MRK' => $mrk->MRK,
            'KTG' => $ktg->KTG,
            'HRG' => 250000,
        ];

        $responseItem = $this->actingAs($this->user)->post(route('inventory.item.store'), $itemData);
        $responseItem->assertSessionHasNoErrors();

        $itm = ITM::where('DES', $itemData['DES'])->first();
        $this->assertNotNull($itm);
        $this->assertEquals(0, $itm->STOK, 'Stok awal barang baru harus 0');

        // 2B. Tambah Stok Barang (Memasukkan unit barang baru ke Gudang)
        $stokData = [
            'stok_itm' => $itm->ITM,
            'stok_spp' => $spp->SPP,
            'stok_gd'  => $gd->GD,
            'stok_hrb' => 180000,
            'stok_bc'  => 'BCIN' . rand(100000, 999999),
        ];

        $responseStok = $this->actingAs($this->user)->post(route('inventory.item.stok'), $stokData);
        $responseStok->assertSessionHasNoErrors();

        // Refresh ITM dan cek ITMDT
        $itm->refresh();
        $this->assertEquals(1, $itm->STOK, 'Stok harus bertambah menjadi 1');

        $itmdt = ITMDT::where('ITM', $itm->ITM)->where('BC_IN', $stokData['stok_bc'])->first();
        $this->assertNotNull($itmdt);
        $this->assertNotEmpty($itmdt->BC_OUT);
        $this->assertEquals($gd->GD, $itmdt->GD);
    }

    /**
     * 3. Test Perpindahan Gudang (Request -> Konfirmasi 1 -> Konfirmasi 2)
     */
    public function test_perpindahan_gudang_flow(): void
    {
        // Setup Gudang Asal & Tujuan
        $gdAsal   = GD::firstOrCreate(['GD' => 'GD_A1'], ['DES' => 'Gudang Cabang A']);
        $gdTujuan = GD::firstOrCreate(['GD' => 'GD_B1'], ['DES' => 'Gudang Cabang B']);
        $spp      = SPP::firstOrCreate(['SPP' => 'SPP_T2'], ['DES' => 'Supplier Perpindahan', 'TLP' => '0822222222']);
        $ktg      = KTG::firstOrCreate(['KTG' => 'KTG_T2'], ['DES' => 'Kategori Perpindahan']);
        $mrk      = MRK::firstOrCreate(['MRK' => 'MRK_T2'], ['DES' => 'Merk Perpindahan']);

        // Buat barang & stok di Gudang Asal
        $itm = ITM::create([
            'ITM'  => 'ITM_TRF' . rand(100, 999),
            'DES'  => 'Speaker Portable ' . rand(1000, 9999),
            'MRK'  => $mrk->MRK,
            'KTG'  => $ktg->KTG,
            'HRG'  => 350000,
            'STOK' => 1,
        ]);

        $bcOut = 'BCO' . rand(10000000, 99999999);
        $itmdt = ITMDT::create([
            'ITM'    => $itm->ITM,
            'BC_IN'  => 'BCIN_TRF' . rand(1000, 9999),
            'BC_OUT' => $bcOut,
            'TM'     => date('Y-m-d'),
            'HRB'    => 200000,
            'GD'     => $gdAsal->GD,
            'SPP'    => $spp->SPP,
        ]);

        // 3A. Request Perpindahan Gudang
        $rpData = [
            'DTRQ'      => date('Y-m-d'),
            'GD_TUJUAN' => $gdTujuan->GD,
            'items'     => [$bcOut],
        ];

        $responseRp = $this->actingAs($this->user)->post(route('inventory.perpindahan.store'), $rpData);
        $responseRp->assertSessionHasNoErrors();

        $rp = RP::where('GD_ASAL', $gdAsal->GD)->where('GD_TUJUAN', $gdTujuan->GD)->latest('created_at')->first();
        $this->assertNotNull($rp);
        $this->assertEquals('Request', $rp->SS);

        $rpd = RPD::where('RP', $rp->RP)->where('BC_OUT', $bcOut)->first();
        $this->assertNotNull($rpd);

        // 3B. Konfirmasi 1 (Barang dikirim)
        $responseKonf1 = $this->actingAs($this->user)->post(route('inventory.perpindahan.konfirmasi1', $rp->RP));
        $responseKonf1->assertSessionHasNoErrors();

        $rp->refresh();
        $this->assertEquals('Pengiriman', $rp->SS);

        // 3C. Konfirmasi 2 (Barang diterima di Gudang Tujuan - langsung selesai tanpa scan 1 per 1)
        $responseKonf2 = $this->actingAs($this->user)->post(route('inventory.perpindahan.konfirmasi2', $rp->RP));
        $responseKonf2->assertSessionHasNoErrors();

        $rp->refresh();
        $itmdt->refresh();

        $this->assertEquals('Diterima', $rp->SS);
        $this->assertEquals($gdTujuan->GD, $itmdt->GD, 'Lokasi gudang barang di ITMDT harus berpindah ke Gudang Tujuan');
    }

    /**
     * 4. Test Alur Transaksi Kasir / Beli Barang & Laporan Kasir
     */
    public function test_transaksi_kasir_dan_laporan_flow(): void
    {
        // Pastikan tidak ada shift kasir terbuka lain
        KAS::whereNull('TGLK')->update(['TGLK' => now()]);

        // Setup data barang untuk dibeli
        $ktg = KTG::firstOrCreate(['KTG' => 'KTG_T3'], ['DES' => 'Kategori Kasir']);
        $mrk = MRK::firstOrCreate(['MRK' => 'MRK_T3'], ['DES' => 'Merk Kasir']);
        $spp = SPP::firstOrCreate(['SPP' => 'SPP_T3'], ['DES' => 'Supplier Kasir', 'TLP' => '0833333333']);
        $gd  = GD::firstOrCreate(['GD' => 'GD_K1'], ['DES' => 'Gudang Kasir']);

        $itm = ITM::create([
            'ITM'  => 'ITM_BUY' . rand(100, 999),
            'DES'  => 'Smart Watch ' . rand(1000, 9999),
            'MRK'  => $mrk->MRK,
            'KTG'  => $ktg->KTG,
            'HRG'  => 500000,
            'STOK' => 2,
        ]);

        $bcOut1 = 'BCO_BUY_' . rand(100000, 999999);
        $bcOut2 = 'BCO_BUY_' . rand(100000, 999999);

        ITMDT::create([
            'ITM'    => $itm->ITM,
            'BC_IN'  => 'BCIN_B1',
            'BC_OUT' => $bcOut1,
            'TM'     => date('Y-m-d'),
            'HRB'    => 350000,
            'GD'     => $gd->GD,
            'SPP'    => $spp->SPP,
        ]);

        ITMDT::create([
            'ITM'    => $itm->ITM,
            'BC_IN'  => 'BCIN_B2',
            'BC_OUT' => $bcOut2,
            'TM'     => date('Y-m-d'),
            'HRB'    => 350000,
            'GD'     => $gd->GD,
            'SPP'    => $spp->SPP,
        ]);

        // 4A. Buka Kasir dengan Modal Opening Rp 100.000
        $bukaKasirResponse = $this->actingAs($this->user)->postJson(route('POS.buka-kasir'), [
            'kas_opening' => '100000',
        ]);
        $bukaKasirResponse->assertOk();
        $bukaKasirResponse->assertJson(['success' => true]);

        $activeKas = KAS::whereNull('TGLK')->latest('TGLM')->first();
        $this->assertNotNull($activeKas);
        $this->assertEquals(100000, (int) $activeKas->AWLT);

        // 4B. Pelanggan Beli Barang (Checkout Tunai)
        $cartItem = [
            'id'       => $itm->ITM,
            'name'     => $itm->DES,
            'price'    => 500000,
            'qty'      => 1,
            'code'     => $bcOut1,
            'discount' => 0,
        ];

        $bayarData = [
            'nominal_bayar'   => 500000,
            'payment_method'  => 'tunai',
            'cart'            => [$bcOut1 => $cartItem],
            'grand_total'     => 500000,
        ];

        $bayarResponse = $this->actingAs($this->user)
            ->withSession(['cart' => [$bcOut1 => $cartItem]])
            ->postJson(route('POS.bayar'), $bayarData);

        $bayarResponse->assertOk();
        $bayarResponse->assertJson(['success' => true]);

        // Verifikasi transaksi ACR & STS tersimpan
        $refNo = $bayarResponse->json('ref_no');
        $acr = ACR::where('REF_NO', $refNo)->first();
        $this->assertNotNull($acr);
        $this->assertEquals(500000, (int) $acr->BRT);
        $this->assertEquals(1, (int) $acr->JNS, 'JNS harus 1 untuk pembayaran tunai');

        $sts = STS::where('REF_NO', $refNo)->where('BC_OUT', $bcOut1)->first();
        $this->assertNotNull($sts);

        // Stok barang berkurang dari 2 menjadi 1
        $itm->refresh();
        $this->assertEquals(1, $itm->STOK, 'Stok barang harus berkurang setelah terjual');

        // Kas aktif terupdate penjualan tunai
        $activeKas->refresh();
        $this->assertEquals(500000, (int) $activeKas->DBT);

        // 4C. Tutup Kasir dengan Kas Akhir Fisik Tunai Rp 580.000
        // (Seharusnya kas tunai: Modal 100.000 + Penjualan 500.000 = 600.000. Fisik 580.000 => Selisih -20.000 defisit)
        $tutupResponse = $this->actingAs($this->user)->postJson(route('POS.tutup-kasir'), [
            'nominal_akhir_tunai' => '580000',
        ]);
        $tutupResponse->assertOk();
        $tutupResponse->assertJson(['success' => true]);

        $activeKas->refresh();
        $this->assertNotNull($activeKas->TGLK);
        $this->assertEquals(580000, (int) $activeKas->AKHT, 'Uang akhir tunai fisik harus tersimpan di AKHT');

        // 4D. Verifikasi Halaman Laporan Kasir
        $laporanResponse = $this->actingAs($this->user)->get(route('POS.laporan'));
        $laporanResponse->assertOk();

        // Pastikan kolom baru ada di view laporan
        $laporanResponse->assertSee('Total Tunai');
        $laporanResponse->assertSee('Total Non Tunai');
        $laporanResponse->assertSee('Uang Akhir Tunai');
        $laporanResponse->assertSee('Selisih');
        $laporanResponse->assertDontSee('Kode Kas');
        $laporanResponse->assertDontSee('Kas Akhir Rill');

        // Cek data selisih murni uang tunai di baris laporan
        // Selisih = 580.000 - (100.000 + 500.000 - 0) = -20.000
        // Nominal ditampilkan positif Rp 20.000 dengan class text-red-600
        $laporanResponse->assertSee('Rp 580.000'); // Uang akhir tunai
        $laporanResponse->assertSee('Rp 20.000');  // Nilai selisih (abs)
        $laporanResponse->assertSee('text-red-600 font-semibold'); // Warna merah karena defisit
    }

    /**
     * 5. Test Opname Stok / Cek Stok
     */
    public function test_opname_stok_flow(): void
    {
        $ktg = KTG::firstOrCreate(['KTG' => 'KTG_T4'], ['DES' => 'Kategori Opname']);
        $mrk = MRK::firstOrCreate(['MRK' => 'MRK_T4'], ['DES' => 'Merk Opname']);
        $gd  = GD::firstOrCreate(['GD' => 'GD_O1'], ['DES' => 'Gudang Opname']);
        $spp = SPP::firstOrCreate(['SPP' => 'SPP_T4'], ['DES' => 'Supplier Opname', 'TLP' => '0844444444']);

        $itm = ITM::create([
            'ITM'  => 'ITM_OPN' . rand(100, 999),
            'DES'  => 'Powerbank Opname ' . rand(1000, 9999),
            'MRK'  => $mrk->MRK,
            'KTG'  => $ktg->KTG,
            'HRG'  => 150000,
            'STOK' => 1,
        ]);

        $bcOut = 'BCO_OPN_' . rand(100000, 999999);
        ITMDT::create([
            'ITM'    => $itm->ITM,
            'BC_IN'  => 'BCIN_OPN',
            'BC_OUT' => $bcOut,
            'TM'     => date('Y-m-d'),
            'HRB'    => 100000,
            'GD'     => $gd->GD,
            'SPP'    => $spp->SPP,
        ]);

        $responseOpname = $this->actingAs($this->user)->get(route('inventory.opname.pull', ['itm' => $itm->ITM]));
        $responseOpname->assertOk();
        $responseOpname->assertSee($itm->DES);
        $responseOpname->assertSee($bcOut);
    }

    /**
     * 6. Test Transaksi Non-Tunai, Edit Transaksi, dan Selisih Laporan Kasir
     */
    public function test_transaksi_non_tunai_dan_edit_transaksi_flow(): void
    {
        // Pastikan tidak ada shift kasir terbuka lain
        KAS::whereNull('TGLK')->update(['TGLK' => now()]);

        // Setup master data
        $ktg = KTG::firstOrCreate(['KTG' => 'KTG_T5'], ['DES' => 'Kategori NonTunai']);
        $mrk = MRK::firstOrCreate(['MRK' => 'MRK_T5'], ['DES' => 'Merk NonTunai']);
        $gd  = GD::firstOrCreate(['GD' => 'GD_NT1'], ['DES' => 'Gudang NonTunai']);
        $spp = SPP::firstOrCreate(['SPP' => 'SPP_T5'], ['DES' => 'Supplier NonTunai', 'TLP' => '0855555555']);

        $itm = ITM::create([
            'ITM'  => 'ITM_NT' . rand(100, 999),
            'DES'  => 'Tablet Android ' . rand(1000, 9999),
            'MRK'  => $mrk->MRK,
            'KTG'  => $ktg->KTG,
            'HRG'  => 1200000,
            'STOK' => 1,
        ]);

        $bcOut = 'BCO_NT_' . rand(100000, 999999);
        ITMDT::create([
            'ITM'    => $itm->ITM,
            'BC_IN'  => 'BCIN_NT1',
            'BC_OUT' => $bcOut,
            'TM'     => date('Y-m-d'),
            'HRB'    => 900000,
            'GD'     => $gd->GD,
            'SPP'    => $spp->SPP,
        ]);

        // 6A. Buka Kasir
        $this->actingAs($this->user)->postJson(route('POS.buka-kasir'), [
            'kas_opening' => '200000',
        ]);
        $activeKas = KAS::whereNull('TGLK')->latest('TGLM')->first();

        // 6B. Pembayaran NON-TUNAI (Transfer / QRIS)
        $cartItem = [
            'id'       => $itm->ITM,
            'name'     => $itm->DES,
            'price'    => 1200000,
            'qty'      => 1,
            'code'     => $bcOut,
            'discount' => 0,
        ];

        $bayarResponse = $this->actingAs($this->user)
            ->withSession(['cart' => [$bcOut => $cartItem]])
            ->postJson(route('POS.bayar'), [
                'nominal_bayar'   => 1200000,
                'payment_method'  => 'non-tunai',
                'cart'            => [$bcOut => $cartItem],
                'grand_total'     => 1200000,
            ]);

        $bayarResponse->assertOk();
        $refNo = $bayarResponse->json('ref_no');

        // Cek ACR & STS
        $acr = ACR::where('REF_NO', $refNo)->first();
        $this->assertEquals(2, (int) $acr->JNS, 'JNS harus 2 untuk pembayaran non-tunai');
        $this->assertEquals(1200000, (int) $acr->BRT);

        // Cek kas: non tunai bertambah di DBNT, tunai DBT tetap 0
        $activeKas->refresh();
        $this->assertEquals(0, (int) $activeKas->DBT, 'DBT harus tetap 0');
        $this->assertEquals(1200000, (int) $activeKas->DBNT, 'DBNT harus 1200000');

        // 6C. Edit Transaksi (Ubah diskon / harga jual transaksi)
        $stsItem = STS::where('REF_NO', $refNo)->first();
        $editPayload = [
            'items' => [
                [
                    'rec'   => $stsItem->REC,
                    'hgj'   => '1100000',
                    'pot_1' => 0,
                    'pot_r' => 0,
                ],
            ],
            'discount_percent' => 0,
            'voucher'          => 0,
            'ppn'              => 0,
        ];

        $updateTransaksiResponse = $this->actingAs($this->user)
            ->put(route('POS.transaksi.update', urlencode($refNo)), $editPayload);
        $updateTransaksiResponse->assertSessionHasNoErrors();

        // Verifikasi ACR & STS setelah di-edit
        $acr->refresh();
        $stsItem->refresh();
        $this->assertEquals(1100000, (int) $acr->BRT, 'Total transaksi ACR harus terupdate menjadi 1.100.000');
        $this->assertEquals(1100000, (int) $stsItem->HGJ, 'Harga STS harus terupdate menjadi 1.100.000');

        // 6D. Tutup Kasir dan Pastikan Selisih Laporan Kasir HANYA Menghitung Kas Tunai
        // Kas Awal Tunai = 200.000. Penjualan Tunai = 0. Kas Tunai Seharusnya = 200.000.
        // Kasir memasukkan fisik tunai = 200.000 (selisih tunai harus 0, non-tunai tidak boleh bikin selisih)
        $tutupResponse = $this->actingAs($this->user)->postJson(route('POS.tutup-kasir'), [
            'nominal_akhir_tunai' => '200000',
        ]);
        $tutupResponse->assertOk();

        $laporanResponse = $this->actingAs($this->user)->get(route('POS.laporan'));
        $laporanResponse->assertOk();

        // Di laporan kasir:
        // Kas awal = 200.000
        // Tunai = 0
        // Non Tunai = 1.100.000
        // Uang Akhir Tunai = 200.000
        // Selisih = 200.000 - 200.000 = 0 (tidak boleh ada selisih jutaan dari non-tunai)
        $laporanResponse->assertSee('Rp 200.000'); // Kas awal dan uang akhir tunai
        $laporanResponse->assertSee('Rp 1.100.000'); // Non tunai
    }
}
