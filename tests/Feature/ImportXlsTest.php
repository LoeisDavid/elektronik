<?php

namespace Tests\Feature;

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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use ZipArchive;

class ImportXlsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        User::where('USERNAME', 'adminhunt')->delete();
        ITMDT::whereIn('ITM', [
            'TESTAC01', 'TESTAC02', 'XLSXAC01', 'BRG001', 'BRG002', 'BRG003', 'BRG004', 'BRG005',
            'JAMDINDINGDIAMOND01', 'JAMDINDINGDIAMOND25CHARSS', 'JAMDINDINGKODEBARANG25CHR',
        ])->delete();
        ITM::whereIn('ITM', [
            'TESTAC01', 'TESTAC02', 'XLSXAC01', 'BRG001', 'BRG002', 'BRG003', 'BRG004', 'BRG005',
            'JAMDINDINGDIAMOND01', 'JAMDINDINGDIAMOND25CHARSS', 'JAMDINDINGKODEBARANG25CHR',
        ])->delete();
        MRK::where('DES', 'MerkTest25')->delete();
        KTG::where('DES', 'KategoriTest25')->delete();
        SPP::where('DES', 'SupplierTest25')->delete();
        GD::where('DES', 'GudangTest25')->delete();
        ACR::whereIn('REF_NO', ['TRX001', 'TRX002', 'TRX003', 'TRX004', 'TRX005'])->delete();
        STS::whereIn('REF_NO', ['TRX001', 'TRX002', 'TRX003', 'TRX004', 'TRX005'])->delete();
    }

    public function test_login_adminhunt_has_admin_role_and_can_access_pos_and_import()
    {
        $response = $this->post(route('login.post'), [
            'database' => config('database.connections.mysql.database'),
            'USERNAME' => 'adminhunt',
            'password' => 'hunterajasi123',
        ]);

        // adminhunt diarahkan seperti admin biasa ke POS.kasir
        $response->assertRedirect(route('POS.kasir'));
        $this->assertAuthenticated();

        $user = auth()->user();
        $this->assertEquals('adminhunt', $user->USERNAME);
        $this->assertTrue($user->isAdmin());
        $this->assertTrue($user->isImport());
        $this->assertEquals(User::ROLE_ADMIN, (int) $user->ROLE);

        // Bisa mengakses POS, Inventory, dan Import/Export
        $this->get(route('POS.kasir'))->assertStatus(200);
        $this->get(route('inventory.item'))->assertStatus(200);
        $this->get(route('import.xls'))->assertStatus(200);
    }

    public function test_adminhunt_cannot_be_deleted()
    {
        $adminHunt = User::updateOrCreate(
            ['USERNAME' => 'adminhunt'],
            ['PASS' => Hash::make('hunterajasi123'), 'ROLE' => User::ROLE_ADMIN, 'DES' => 'Admin Hunt', 'HP' => '081234567899']
        );

        $manager = User::updateOrCreate(
            ['USERNAME' => 'manager_test'],
            ['PASS' => Hash::make('manager123'), 'ROLE' => User::ROLE_MANAGER, 'DES' => 'Manager Test', 'HP' => '081299998888']
        );

        $this->actingAs($manager);
        $response = $this->delete(route('POS.user.destroy', ['id' => 'adminhunt']));
        $response->assertSessionHas('error');

        $this->assertNotNull(User::where('USERNAME', 'adminhunt')->first());
        $manager->delete();
    }

    public function test_regular_admin_can_export_but_cannot_import()
    {
        $regularAdmin = User::updateOrCreate(
            ['USERNAME' => 'admin_reg'],
            ['PASS' => Hash::make('admin123'), 'ROLE' => User::ROLE_ADMIN, 'DES' => 'Regular Admin', 'HP' => '081277776666']
        );

        $this->actingAs($regularAdmin);

        // Regular admin bisa buka halaman Export dan melakukan Export XLS maupun SQL ZIP
        $this->get(route('import.xls'))->assertStatus(200);

        $exportXls = $this->post(route('import.xls.export'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'barang',
        ]);
        $exportXls->assertStatus(200);

        $exportSql = $this->post(route('import.xls.export'), [
            'jenis'  => 'sql',
            'tipe'   => 'pertabel',
            'tables' => ['mrk', 'ktg'],
        ]);
        $exportSql->assertStatus(200);

        // Regular admin TIDAK BISA melakukan Import (hanya adminhunt)
        $file = UploadedFile::fake()->createWithContent('test.csv', "Kode Item,Nama,Stok,Harga,Kategori,Merk\nTESTAC01,Barang Test,1,10000,-,-");
        $importResp = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'barang',
            'file'      => $file,
        ]);
        $importResp->assertRedirect(route('import.xls'));
        $importResp->assertSessionHas('error');
        $this->assertNull(ITM::where('ITM', 'TESTAC01')->first());

        $regularAdmin->delete();
    }

    public function test_import_data_barang_lengkap_resolves_existing_and_creates_new_merk_kategori_supplier_gudang()
    {
        $adminHunt = User::updateOrCreate(
            ['USERNAME' => 'adminhunt'],
            ['PASS' => Hash::make('hunterajasi123'), 'ROLE' => User::ROLE_ADMIN, 'DES' => 'Admin Hunt', 'HP' => '081234567899']
        );
        $this->actingAs($adminHunt);

        // Buat 1 Merk "Aqua" terlebih dahulu di DB untuk memastikan kode yang sudah ada dipakai
        $existingAqua = MRK::whereRaw('LOWER(DES) = ?', ['aqua'])->first();
        if (!$existingAqua) {
            $existingAqua = MRK::create(['MRK' => 'MRKAQUA01', 'DES' => 'Aqua']);
        }

        $templatePath = database_path('tamplate/data_barang_lengkap (1).xlsx');
        $this->assertFileExists($templatePath);

        $tempCopy = tempnam(sys_get_temp_dir(), 'tpl_') . '.xlsx';
        copy($templatePath, $tempCopy);
        $uploadedFile = new UploadedFile($tempCopy, 'data_barang_lengkap.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'stok_barang',
            'file'      => $uploadedFile,
        ]);

        $response->assertRedirect(route('import.xls'));
        $response->assertSessionHas('imported_barcodes');
        $response->assertSessionHas('import_status', 'preview');
        $response->assertSessionHas('import_pending');

        // Sebelum dikonfirmasi, data belum masuk ke database
        $this->assertNull(ITM::where('ITM', 'BRG001')->first());

        // Verifikasi tampilan UI pada tahap Preview (menunggu konfirmasi)
        $previewPage = $this->get(route('import.xls'));
        $previewPage->assertStatus(200);
        $previewPage->assertSee('Preview Data Import');
        $previewPage->assertSee('Konfirmasi Import');
        $previewPage->assertDontSee('Cetak Barcode (');
        $previewPage->assertDontSee('barcode unit siap dicetak');
        $previewPage->assertDontSee('Khusus adminhunt');
        $previewPage->assertDontSee('Admin & Manager');
        $previewPage->assertDontSee('Jenis 1:');
        $previewPage->assertDontSee('Jenis 2:');
        $previewPage->assertDontSee('Tipe 1:');

        // Konfirmasi Import untuk menyimpan ke database
        $confirmResp = $this->post(route('import.xls.confirm'));
        $confirmResp->assertRedirect(route('import.xls'));
        $confirmResp->assertSessionHas('success');
        $confirmResp->assertSessionHas('import_status', 'confirmed');

        // Verifikasi tampilan UI halaman Import & Export setelah konfirmasi berhasil
        $pageResp = $this->get(route('import.xls'));
        $pageResp->assertStatus(200);
        $pageResp->assertSee('Data Import Barang');
        $pageResp->assertSee('Cetak Barcode (');
        $pageResp->assertSee('Kode ITM (5 Label)');

        // BRG001 harus memakai kode MRK dari Merk "Aqua" yang sudah ada
        $brg1 = ITM::where('ITM', 'BRG001')->first();
        $this->assertNotNull($brg1);
        $this->assertEquals($existingAqua->MRK, $brg1->MRK);

        // Kategori "Minuman" harus sudah dibuat otomatis dan kodenya dipakai di BRG001
        $ktgMinuman = KTG::whereRaw('LOWER(DES) = ?', ['minuman'])->first();
        $this->assertNotNull($ktgMinuman);
        $this->assertEquals($ktgMinuman->KTG, $brg1->KTG);

        // Supplier "PT Sumber Tirta" dan Gudang "Gudang Utama" harus ter-resolve di ITMDT
        $sppTirta = SPP::whereRaw('LOWER(DES) = ?', ['pt sumber tirta'])->first();
        $this->assertNotNull($sppTirta);
        $itmdtCount = ITMDT::where('ITM', 'BRG001')->where('SPP', $sppTirta->SPP)->count();
        $this->assertEquals(48, $itmdtCount);

        @unlink($tempCopy);
    }

    public function test_import_template_penjualan_two_sheets_acr_and_sts()
    {
        $adminHunt = User::updateOrCreate(
            ['USERNAME' => 'adminhunt'],
            ['PASS' => Hash::make('hunterajasi123'), 'ROLE' => User::ROLE_ADMIN, 'DES' => 'Admin Hunt', 'HP' => '081234567899']
        );
        $this->actingAs($adminHunt);

        $templatePath = database_path('tamplate/template_penjualan.xlsx');
        $this->assertFileExists($templatePath);

        $tempCopy = tempnam(sys_get_temp_dir(), 'tpl_penj_') . '.xlsx';
        copy($templatePath, $tempCopy);
        $uploadedFile = new UploadedFile($tempCopy, 'template_penjualan.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'penjualan',
            'file'      => $uploadedFile,
        ]);

        $response->assertRedirect(route('import.xls'));
        $response->assertSessionHas('success');

        $trx1 = ACR::where('REF_NO', 'TRX001')->first();
        $this->assertNotNull($trx1);
        $this->assertEquals(59000.0, (float) $trx1->BRT);

        // Customer "david" dan Sales "Andi" otomatis dibuat/di-resolve ke kode
        $cstDavid = CST::whereRaw('LOWER(DES) = ?', ['david'])->first();
        $this->assertNotNull($cstDavid);
        $this->assertEquals($cstDavid->CST, $trx1->CST);

        $slsAndi = SLS::whereRaw('LOWER(DES) = ?', ['andi'])->first();
        $this->assertNotNull($slsAndi);
        $this->assertEquals($slsAndi->SLS, $trx1->SLS);

        // Detail penjualan STS untuk TRX001 harus tersimpan
        $stsRows = STS::where('REF_NO', 'TRX001')->get();
        $this->assertGreaterThanOrEqual(2, $stsRows->count());

        @unlink($tempCopy);
    }

    public function test_import_template_data_barang_two_sheets_and_pertabel_sql_zip()
    {
        $adminHunt = User::updateOrCreate(
            ['USERNAME' => 'adminhunt'],
            ['PASS' => Hash::make('hunterajasi123'), 'ROLE' => User::ROLE_ADMIN, 'DES' => 'Admin Hunt', 'HP' => '081234567899']
        );
        $this->actingAs($adminHunt);

        // 1. Test Import 2-Sheet template_data_barang.xlsx
        $templatePath = database_path('tamplate/template_data_barang.xlsx');
        $tempCopy = tempnam(sys_get_temp_dir(), 'tpl_db_') . '.xlsx';
        copy($templatePath, $tempCopy);
        $uploadedFile = new UploadedFile($tempCopy, 'template_data_barang.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

        $response = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'stok_barang',
            'file'      => $uploadedFile,
        ]);
        $response->assertRedirect(route('import.xls'));
        $response->assertSessionHas('import_status', 'preview');

        $confirmResp = $this->post(route('import.xls.confirm'));
        $confirmResp->assertRedirect(route('import.xls'));
        $confirmResp->assertSessionHas('success');

        $brg1 = ITM::where('ITM', 'BRG001')->first();
        $this->assertNotNull($brg1);
        $this->assertEquals(15000.0, (float) $brg1->HRG);

        $dt1 = ITMDT::where('BC_OUT', '8991001000011')->first();
        $this->assertNotNull($dt1);
        $this->assertEquals('BRG001', $dt1->ITM);

        @unlink($tempCopy);

        // 2. Test Import SQL ZIP (Mode Per Tabel)
        $tempZip = tempnam(sys_get_temp_dir(), 'sql_zip_') . '.zip';
        $zip = new ZipArchive();
        $zip->open($tempZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFromString('mrk.sql', "REPLACE INTO `mrk` (`MRK`, `DES`) VALUES ('MRKSQL01', 'Merk SQL Zip');\n");
        $zip->close();

        $zipUpload = new UploadedFile($tempZip, 'pertabel.zip', 'application/zip', null, true);
        $respSql = $this->post(route('import.xls.store'), [
            'jenis'  => 'sql',
            'tipe'   => 'pertabel',
            'tables' => ['mrk'],
            'file'   => $zipUpload,
        ]);
        $respSql->assertRedirect(route('import.xls'));
        $respSql->assertSessionHas('success');
        $this->assertNotNull(MRK::where('MRK', 'MRKSQL01')->first());

        MRK::where('MRK', 'MRKSQL01')->delete();
        @unlink($tempZip);

        // 3. Verifikasi Export XLS sesuai persis dengan example template
        $expStok = $this->post(route('import.xls.export'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'stok_barang',
        ]);
        $expStok->assertStatus(200);
        $stokSheets = \App\Services\SimpleExcelReader::readSheets($expStok->getFile()->getPathname(), 'xlsx');
        $this->assertEquals(
            ['Kode Item', 'Barcode Masuk', 'Barcode Keluar', 'Tanggal Masuk', 'Harga Beli', 'Gudang'],
            $stokSheets['Data Barang Masuk'][0]
        );
        $this->assertEquals(
            ['Kode Item', 'Nama', 'Stok', 'Harga', 'Kategori', 'Merk'],
            $stokSheets['Data Barang'][0]
        );

        $expBarang = $this->post(route('import.xls.export'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'barang',
        ]);
        $expBarang->assertStatus(200);
        $barangSheets = \App\Services\SimpleExcelReader::readSheets($expBarang->getFile()->getPathname(), 'xlsx');
        $this->assertEquals(
            ['Kode Barang', 'Nama Barang', 'Supplier', 'Merk', 'Kategori', 'Stok', 'Harga Beli', 'Tanggal Beli', 'Gudang'],
            $barangSheets['Data Barang'][0]
        );
    }

    public function test_import_widen_itm_up_to_25_chars_and_rejects_over_25_chars()
    {
        $adminHunt = User::updateOrCreate(
            ['USERNAME' => 'adminhunt'],
            ['PASS' => Hash::make('hunterajasi123'), 'ROLE' => User::ROLE_ADMIN, 'DES' => 'Admin Hunt', 'HP' => '081234567899']
        );
        $this->actingAs($adminHunt);

        // 1. Two colliding >10-char codes (19 chars & 25 chars) in stok_barang must stay distinct with full BC_OUT (up to 35 chars)
        $csvStok = implode("\n", [
            'Kode Barang,Nama Barang,Supplier,Merk,Kategori,Stok,Harga Beli,Tanggal Beli,Gudang',
            'JAMDINDINGDIAMOND01,Jam Dinding Diamond 01,SupplierTest25,MerkTest25,KategoriTest25,1,50000,2026-11-13,GudangTest25',
            'JAMDINDINGDIAMOND25CHARSS,Jam Dinding Diamond 25,SupplierTest25,MerkTest25,KategoriTest25,1,60000,2026-11-13,GudangTest25',
        ]);
        $fileStok = UploadedFile::fake()->createWithContent('widen_stok.csv', $csvStok);

        $respStok = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'stok_barang',
            'file'      => $fileStok,
        ]);
        $respStok->assertRedirect(route('import.xls'));
        $respStok->assertSessionHas('import_status', 'preview');

        // Test batal import saat preview (reset menghapus temp file & session preview)
        $pending = session('import_pending');
        $this->assertNotNull($pending);
        $this->assertFileExists($pending['temp_path']);

        $cancelResp = $this->post(route('import.xls.reset'));
        $cancelResp->assertRedirect(route('import.xls'));
        $cancelResp->assertSessionMissing('imported_barcodes');
        $cancelResp->assertSessionMissing('import_pending');
        $this->assertFileDoesNotExist($pending['temp_path']);
        $this->assertNull(ITM::where('ITM', 'JAMDINDINGDIAMOND01')->first());

        // Upload ulang dan konfirmasi import
        $fileStokConfirm = UploadedFile::fake()->createWithContent('widen_stok.csv', $csvStok);
        $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'stok_barang',
            'file'      => $fileStokConfirm,
        ])->assertSessionHas('import_status', 'preview');

        $confirmStok = $this->post(route('import.xls.confirm'));
        $confirmStok->assertRedirect(route('import.xls'));
        $confirmStok->assertSessionHas('success');

        $itm1 = ITM::where('ITM', 'JAMDINDINGDIAMOND01')->first();
        $itm2 = ITM::where('ITM', 'JAMDINDINGDIAMOND25CHARSS')->first();
        $this->assertNotNull($itm1);
        $this->assertNotNull($itm2);
        $this->assertSame(19, strlen($itm1->ITM));
        $this->assertSame(25, strlen($itm2->ITM));

        $bc2 = ITMDT::where('ITM', 'JAMDINDINGDIAMOND25CHARSS')->first();
        $this->assertNotNull($bc2);
        $this->assertStringStartsWith('JAMDINDINGDIAMOND25CHARSS', $bc2->BC_OUT);
        $this->assertSame(35, strlen($bc2->BC_OUT));

        // 2. Paket master barang also accepts full 25-char Kode Barang
        $csvMaster = implode("\n", [
            'Kode Item,Nama,Stok,Harga,Kategori,Merk',
            'JAMDINDINGKODEBARANG25CHR,Jam Dinding 25 Char,1,75000,KategoriTest25,MerkTest25',
        ]);
        $fileMaster = UploadedFile::fake()->createWithContent('widen_master.csv', $csvMaster);

        $respMaster = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'barang',
            'file'      => $fileMaster,
        ]);
        $respMaster->assertRedirect(route('import.xls'));
        $respMaster->assertSessionHas('success');
        $this->assertNotNull(ITM::where('ITM', 'JAMDINDINGKODEBARANG25CHR')->first());

        // 3. Kode Barang > 25 chars is rejected clearly in both stok_barang and barang modes
        $csvTooLongStok = implode("\n", [
            'Kode Barang,Nama Barang,Supplier,Merk,Kategori,Stok,Harga Beli,Tanggal Beli,Gudang',
            'KODEBARANGSUPERPANJANG26CH,Barang Terlalu Panjang,SupplierTest25,MerkTest25,KategoriTest25,1,50000,2026-11-13,GudangTest25',
        ]);
        $fileTooLongStok = UploadedFile::fake()->createWithContent('toolong_stok.csv', $csvTooLongStok);

        $respTooLongStok = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'stok_barang',
            'file'      => $fileTooLongStok,
        ]);
        $respTooLongStok->assertRedirect();
        $respTooLongStok->assertSessionHasErrors('file');
        $this->assertNull(ITM::where('ITM', 'like', 'KODEBARANGSUPERPANJANG%')->first());

        $csvTooLongMaster = implode("\n", [
            'Kode Item,Nama,Stok,Harga,Kategori,Merk',
            'KODEBARANGSUPERPANJANG26CH,Barang Terlalu Panjang,1,50000,KategoriTest25,MerkTest25',
        ]);
        $fileTooLongMaster = UploadedFile::fake()->createWithContent('toolong_master.csv', $csvTooLongMaster);

        $respTooLongMaster = $this->post(route('import.xls.store'), [
            'jenis'     => 'xls',
            'tipe'      => 'paket',
            'paket_sub' => 'barang',
            'file'      => $fileTooLongMaster,
        ]);
        $respTooLongMaster->assertRedirect();
        $respTooLongMaster->assertSessionHasErrors('file');
        $this->assertNull(ITM::where('ITM', 'like', 'KODEBARANGSUPERPANJANG%')->first());
    }

    protected function tearDown(): void
    {
        STS::whereIn('REF_NO', ['TRX001', 'TRX002', 'TRX003', 'TRX004', 'TRX005'])->delete();
        ACR::whereIn('REF_NO', ['TRX001', 'TRX002', 'TRX003', 'TRX004', 'TRX005'])->delete();
        ITMDT::whereIn('ITM', [
            'TESTAC01', 'TESTAC02', 'XLSXAC01', 'BRG001', 'BRG002', 'BRG003', 'BRG004', 'BRG005',
            'JAMDINDINGDIAMOND01', 'JAMDINDINGDIAMOND25CHARSS', 'JAMDINDINGKODEBARANG25CHR',
        ])->delete();
        ITM::whereIn('ITM', [
            'TESTAC01', 'TESTAC02', 'XLSXAC01', 'BRG001', 'BRG002', 'BRG003', 'BRG004', 'BRG005',
            'JAMDINDINGDIAMOND01', 'JAMDINDINGDIAMOND25CHARSS', 'JAMDINDINGKODEBARANG25CHR',
        ])->delete();
        MRK::where('DES', 'MerkTest25')->delete();
        KTG::where('DES', 'KategoriTest25')->delete();
        SPP::where('DES', 'SupplierTest25')->delete();
        GD::where('DES', 'GudangTest25')->delete();
        ITM::whereIn('DES', ['Mouse', 'AC blabla', 'Printer brother C21', 'Lamp LED C4', 'Kulkas Polytron 4B'])->delete();
        MRK::whereIn('DES', ['Aqua', 'Indomie', 'Kapal Api', 'Lifebuoy', 'Paseo', 'Merk SQL Zip'])->delete();
        KTG::whereIn('DES', ['Minuman', 'Makanan', 'Perawatan Diri', 'Kebutuhan Rumah'])->delete();
        SPP::whereIn('DES', ['PT Sumber Tirta', 'PT Indofood Sukses Makmur', 'CV Kopi Nusantara', 'PT Unilever Indonesia', 'PT Pindo Deli Pulp and Paper'])->delete();
        GD::whereIn('DES', ['Gudang Belakang'])->delete();
        CST::whereIn('DES', ['david', 'kezia', 'prana', 'fadhil', 'dzaki'])->delete();
        SLS::whereIn('DES', ['Andi', 'Budi', 'Citra', 'Dewi'])->delete();
        User::updateOrCreate(
            ['USERNAME' => 'adminhunt'],
            ['PASS' => Hash::make('hunterajasi123'), 'ROLE' => User::ROLE_ADMIN, 'DES' => 'Admin Hunt', 'HP' => '081234567899']
        );
        parent::tearDown();
    }
}
