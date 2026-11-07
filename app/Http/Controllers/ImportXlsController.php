<?php

namespace App\Http\Controllers;

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
use App\Services\SimpleExcelReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use ZipArchive;

class ImportXlsController extends Controller
{
    /**
     * Daftar tabel database yang dapat dipilih pada mode Per Tabel & Backup
     */
    public const AVAILABLE_TABLES = [
        'itm'   => 'Master Barang (itm)',
        'itmdt' => 'Stok & Barcode Unit (itmdt)',
        'acr'   => 'Transaksi Penjualan (acr)',
        'sts'   => 'Detail Penjualan (sts)',
        'mrk'   => 'Master Merk (mrk)',
        'ktg'   => 'Master Kategori (ktg)',
        'spp'   => 'Master Supplier (spp)',
        'gd'    => 'Master Gudang (gd)',
        'cst'   => 'Master Customer & Desa (cst)',
        'sls'   => 'Master Sales (sls)',
        'kas'   => 'Kas Kasir (kas)',
        'rp'    => 'Perpindahan Gudang (rp)',
        'rpd'   => 'Detail Perpindahan (rpd)',
        'users' => 'Data User (users)',
    ];

    /**
     * Tampilkan halaman utama Import & Export.
     */
    public function index()
    {
        $importedBarcodes = session('imported_barcodes', []);
        $importSummary    = session('import_summary', null);
        $availableTables  = self::AVAILABLE_TABLES;
        $canImport        = auth()->user()?->isImport() ?? false;

        return view('import.index', compact(
            'importedBarcodes',
            'importSummary',
            'availableTables',
            'canImport'
        ));
    }

    /**
     * Unduh file template XLS resmi dari folder database/tamplate/
     */
    public function downloadTemplate(string $type)
    {
        $map = [
            'barang_lengkap' => 'data_barang_lengkap (1).xlsx',
            'data_barang'    => 'template_data_barang.xlsx',
            'penjualan'      => 'template_penjualan.xlsx',
        ];

        $fileName = $map[$type] ?? null;
        if (!$fileName) {
            return back()->with('error', 'Template tidak ditemukan.');
        }

        $filePath = database_path('tamplate/' . $fileName);
        if (!file_exists($filePath)) {
            return back()->with('error', 'File template belum tersedia di server.');
        }

        return response()->download($filePath, $fileName);
    }

    /**
     * Proses Export Data (3 Tipe x 2 Jenis: XLS & SQL ZIP).
     * Dapat diakses oleh Admin, Manager, dan adminhunt.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->canExport()) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk melakukan Export data.');
        }

        $request->validate([
            'jenis'     => ['required', 'in:xls,sql'],
            'tipe'      => ['required', 'in:pertabel,paket,backup'],
            'paket_sub' => ['nullable', 'in:penjualan,stok_barang,barang'],
            'tables'    => ['nullable', 'array'],
        ]);

        $jenis    = $request->input('jenis'); // 'xls' | 'sql'
        $tipe     = $request->input('tipe');  // 'pertabel' | 'paket' | 'backup'
        $paketSub = $request->input('paket_sub', 'stok_barang');
        $timestamp = date('Ymd_His');

        try {
            // 1. Tentukan daftar tabel atau sheet berdasarkan tipe
            if ($tipe === 'pertabel') {
                $selectedTables = array_values(array_intersect(
                    (array) $request->input('tables', []),
                    array_keys(self::AVAILABLE_TABLES)
                ));
                if (empty($selectedTables)) {
                    return back()->withErrors(['tables' => 'Pilih minimal 1 tabel untuk di-export pada mode Per Tabel.']);
                }

                if ($jenis === 'sql') {
                    return $this->exportSqlZip($selectedTables, "export_pertabel_{$timestamp}.zip");
                }
                return $this->exportTablesXlsx($selectedTables, "export_pertabel_{$timestamp}.xlsx");
            }

            if ($tipe === 'paket') {
                if ($paketSub === 'penjualan') {
                    if ($jenis === 'sql') {
                        return $this->exportSqlZip(['acr', 'sts'], "export_penjualan_{$timestamp}.zip");
                    }
                    return $this->exportPaketPenjualanXlsx("export_penjualan_{$timestamp}.xlsx");
                }

                if ($paketSub === 'stok_barang') {
                    if ($jenis === 'sql') {
                        return $this->exportSqlZip(['itm', 'itmdt', 'mrk', 'ktg', 'spp', 'gd'], "export_stok_barang_{$timestamp}.zip");
                    }
                    return $this->exportPaketStokBarangXlsx("export_stok_barang_{$timestamp}.xlsx");
                }

                // paketSub === 'barang'
                if ($jenis === 'sql') {
                    return $this->exportSqlZip(['itm', 'itmdt', 'mrk', 'ktg', 'spp', 'gd'], "export_data_barang_lengkap_{$timestamp}.zip");
                }
                return $this->exportPaketBarangXlsx("export_data_barang_lengkap_{$timestamp}.xlsx");
            }

            // Tipe 3: Backup Semuanya
            $allTables = array_keys(self::AVAILABLE_TABLES);
            if ($jenis === 'sql') {
                return $this->exportSqlZip($allTables, "backup_full_sql_{$timestamp}.zip", true);
            }
            return $this->exportTablesXlsx($allTables, "backup_full_excel_{$timestamp}.xlsx");
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal melakukan export data: ' . $e->getMessage());
        }
    }

    /**
     * Proses Import Data (3 Tipe x 2 Jenis: XLS & SQL).
     * HANYA dapat dilakukan oleh akun khusus adminhunt.
     */
    public function import(Request $request)
    {
        $user = auth()->user();
        if (!$user || !$user->isImport()) {
            return redirect()->route('import.xls')->with('error', 'Fitur Import hanya dapat diakses oleh akun khusus adminhunt.');
        }

        $request->validate([
            'file'      => ['required', 'file', 'max:51200'], // maks 50MB
            'jenis'     => ['nullable', 'in:xls,sql'],
            'tipe'      => ['nullable', 'in:pertabel,paket,backup'],
            'paket_sub' => ['nullable', 'in:penjualan,stok_barang,barang'],
            'tables'    => ['nullable', 'array'],
        ], [
            'file.required' => 'Pilih file (.xlsx, .xls, .csv, .sql, atau .zip) terlebih dahulu.',
            'file.file'     => 'File yang diunggah tidak valid.',
            'file.max'      => 'Ukuran file maksimal 50MB.',
        ]);

        $uploadedFile = $request->file('file');
        $ext = strtolower($uploadedFile->getClientOriginalExtension());
        $jenis = $request->input('jenis') ?: (in_array($ext, ['sql', 'zip'], true) ? 'sql' : 'xls');
        $tipe  = $request->input('tipe', 'paket');
        $paketSub = $request->input('paket_sub', 'stok_barang');

        @set_time_limit(300);

        // =========================================================
        // CABANG 1: IMPORT JENIS SQL (.sql atau .zip)
        // =========================================================
        if ($jenis === 'sql') {
            if (!in_array($ext, ['sql', 'zip'], true)) {
                return back()->withErrors([
                    'file' => "Untuk jenis SQL, unggah file berformat .sql atau .zip (file Anda: .{$ext}).",
                ]);
            }

            try {
                $allowedTables = null;
                if ($tipe === 'pertabel') {
                    $allowedTables = array_values(array_intersect(
                        (array) $request->input('tables', []),
                        array_keys(self::AVAILABLE_TABLES)
                    ));
                    if (empty($allowedTables)) {
                        return back()->withErrors(['tables' => 'Pilih minimal 1 tabel untuk di-import pada mode Per Tabel.']);
                    }
                } elseif ($tipe === 'paket') {
                    $allowedTables = match ($paketSub) {
                        'penjualan'   => ['acr', 'sts', 'cst', 'sls', 'itm'],
                        'stok_barang' => ['itm', 'itmdt', 'mrk', 'ktg', 'spp', 'gd'],
                        'barang'      => ['itm', 'mrk', 'ktg'],
                        default       => null,
                    };
                }

                $executedCount = $this->importSqlFileOrZip($uploadedFile->getRealPath(), $ext, $allowedTables);

                return redirect()->route('import.xls')->with(
                    'success',
                    "Import SQL ({$tipe}) berhasil dieksekusi ({$executedCount} berkas/statement SQL diproses)!"
                );
            } catch (\Throwable $e) {
                return back()->withErrors([
                    'file' => 'Gagal mengimpor file SQL: ' . $e->getMessage(),
                ]);
            }
        }

        // =========================================================
        // CABANG 2: IMPORT JENIS XLS (.xlsx, .xls, .csv)
        // =========================================================
        $validXlsExts = ['xlsx', 'xls', 'csv', 'tsv', 'txt'];
        if (!in_array($ext, $validXlsExts, true)) {
            return back()->withErrors([
                'file' => 'Ekstensi file tidak didukung (' . $ext . '). Gunakan format .xlsx, .xls, atau .csv.',
            ]);
        }

        try {
            $sheets = SimpleExcelReader::readSheets($uploadedFile->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => 'Gagal membaca isi file Excel: ' . $e->getMessage(),
            ]);
        }

        if (empty($sheets)) {
            return back()->withErrors([
                'file' => 'File Excel kosong atau tidak memiliki baris data.',
            ]);
        }

        try {
            // TIPE 1: PER TABEL
            if ($tipe === 'pertabel') {
                $selectedTables = array_values(array_intersect(
                    (array) $request->input('tables', []),
                    array_keys(self::AVAILABLE_TABLES)
                ));
                if (empty($selectedTables)) {
                    return back()->withErrors(['tables' => 'Pilih minimal 1 tabel pada mode Import Per Tabel.']);
                }

                $result = $this->importPerTabelXls($sheets, $selectedTables);

                if (!empty($result['barcodes'])) {
                    session()->put('imported_barcodes', $result['barcodes']);
                    session()->put('import_summary', [
                        'total_barang'   => $result['items_count'],
                        'total_stok'     => count($result['barcodes']),
                        'total_barcodes' => count($result['barcodes']),
                    ]);
                }

                return redirect()->route('import.xls')->with(
                    'success',
                    "Berhasil mengimpor {$result['rows_count']} baris data ke tabel: " . implode(', ', $selectedTables) . "!"
                );
            }

            // TIPE 3: BACKUP SEMUA
            if ($tipe === 'backup') {
                $allTables = ['mrk', 'ktg', 'spp', 'gd', 'cst', 'sls', 'users', 'itm', 'itmdt', 'acr', 'sts', 'kas', 'rp', 'rpd'];
                $result = $this->importPerTabelXls($sheets, $allTables, true);

                return redirect()->route('import.xls')->with(
                    'success',
                    "Restore Backup XLS selesai! Total {$result['rows_count']} baris data berhasil dipulihkan."
                );
            }

            // TIPE 2: PAKET (penjualan | stok_barang | barang)
            if ($paketSub === 'penjualan') {
                $stat = $this->importPaketPenjualanXls($sheets);
                return redirect()->route('import.xls')->with(
                    'success',
                    "Import Penjualan berhasil! {$stat['acr']} transaksi (ACR) dan {$stat['sts']} detail item (STS) telah disimpan."
                );
            }

            if ($paketSub === 'barang') {
                $stat = $this->importPaketBarangXls($sheets);
                if (!empty($stat['barcodes'])) {
                    session()->put('imported_barcodes', $stat['barcodes']);
                    session()->put('import_summary', [
                        'total_barang'   => $stat['total_barang'],
                        'total_stok'     => $stat['total_stok'],
                        'total_barcodes' => count($stat['barcodes']),
                    ]);
                    return redirect()->route('import.xls')->with(
                        'success',
                        "Berhasil mengimpor {$stat['total_barang']} data barang lengkap dengan {$stat['total_stok']} unit barcode!"
                    );
                }
                return redirect()->route('import.xls')->with(
                    'success',
                    "Import Data Barang (ITM) berhasil! {$stat['itm']} barang telah disimpan."
                );
            }

            // Default Paket: stok_barang (ITM + ITMDT + Barcode)
            $stat = $this->importPaketStokBarangXls($sheets);

            if (!empty($stat['barcodes'])) {
                session()->put('imported_barcodes', $stat['barcodes']);
                session()->put('import_summary', [
                    'total_barang'   => $stat['total_barang'],
                    'total_stok'     => $stat['total_stok'],
                    'total_barcodes' => count($stat['barcodes']),
                ]);
            }

            return redirect()->route('import.xls')->with(
                'success',
                "Berhasil mengimpor {$stat['total_barang']} barang dengan total {$stat['total_stok']} unit barcode!"
            );
        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => 'Terjadi kesalahan saat memproses data ke database: ' . $e->getMessage(),
            ]);
        }
    }

    // =========================================================================
    // LOGIKA IMPORT PAKET 2A: PENJUALAN (ACR & STS)
    // =========================================================================
    private function importPaketPenjualanXls(array $sheets): array
    {
        $acrSheet = null;
        $stsSheet = null;

        foreach ($sheets as $name => $rows) {
            $lower = strtolower(trim($name));
            if (str_contains($lower, 'detail') || $lower === 'sts') {
                $stsSheet = $rows;
            } elseif (str_contains($lower, 'penjualan') || $lower === 'acr') {
                $acrSheet = $rows;
            }
        }

        $sheetValues = array_values($sheets);
        if ($acrSheet === null && isset($sheetValues[0])) {
            $acrSheet = $sheetValues[0];
        }
        if ($stsSheet === null && isset($sheetValues[1])) {
            $stsSheet = $sheetValues[1];
        }

        $acrCount = 0;
        $stsCount = 0;

        DB::transaction(function () use ($acrSheet, $stsSheet, &$acrCount, &$stsCount) {
            if (!empty($acrSheet)) {
                $acrCount = $this->processAcrRows($acrSheet);
            }
            if (!empty($stsSheet)) {
                $stsCount = $this->processStsRows($stsSheet);
            }
        });

        return ['acr' => $acrCount, 'sts' => $stsCount];
    }

    private function processAcrRows(array $rows): int
    {
        if (count($rows) < 2) return 0;
        $header = array_map(fn($h) => strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $h)), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $findCol = function (array $candidates, int $default) use ($header): int {
            foreach ($header as $idx => $h) {
                if (in_array($h, $candidates, true)) return $idx;
            }
            return $default;
        };

        $cRef   = $findCol(['nomorreferensi', 'refno', 'noref', 'notransaksi'], 0);
        $cDt    = $findCol(['tanggaltransaksi', 'dt', 'tanggal', 'tgl'], 1);
        $cCst   = $findCol(['kodecustomer', 'customer', 'cst', 'pelanggan'], 2);
        $cSls   = $findCol(['sales', 'sls', 'kodesales'], 3);
        $cBrt   = $findCol(['nilaibruto', 'brt', 'bruto', 'total'], 4);
        $cPay   = $findCol(['pembayaran', 'pay', 'bayar'], 5);
        $cTotP  = $findCol(['totalpotongan', 'totpot'], 6);
        $cJns   = $findCol(['jenispembayaran', 'jns', 'metode'], 7);
        $cPot1  = $findCol(['potonganpersen1', 'pot1'], 8);
        $cPot2  = $findCol(['potonganpersen2', 'pot2'], 9);
        $cPotR  = $findCol(['potongan', 'potr', 'potonganrupiah'], 10);
        $cUser  = $findCol(['user', 'kasir', 'username'], 11);

        $count = 0;
        foreach ($dataRows as $r) {
            $refRaw = trim((string) ($r[$cRef] ?? ''));
            if ($refRaw === '') continue;

            $refNo  = strtoupper(substr($refRaw, 0, 10));
            $dt     = SimpleExcelReader::parseDate($r[$cDt] ?? null);
            $cstObj = $this->resolveCst($r[$cCst] ?? '-');
            $slsObj = $this->resolveSls($r[$cSls] ?? '-');

            $brt    = max(0, SimpleExcelReader::parseNumber($r[$cBrt] ?? 0));
            $totPot = max(0, SimpleExcelReader::parseNumber($r[$cTotP] ?? 0));
            $pot1   = (int) round(SimpleExcelReader::parseNumber($r[$cPot1] ?? 0));
            $pot2   = (int) round(SimpleExcelReader::parseNumber($r[$cPot2] ?? 0));
            $potR   = max(0, SimpleExcelReader::parseNumber($r[$cPotR] ?? 0));

            // Jika kolom PAY berisi teks seperti "Tunai" / "Transfer", hitung PAY = BRT - TOT_POT
            $payRaw = trim((string) ($r[$cPay] ?? ''));
            if ($payRaw !== '' && ! preg_match('/\d/', $payRaw)) {
                $pay = max(0, $brt - $totPot);
            } else {
                $pay = max(0, SimpleExcelReader::parseNumber($payRaw));
            }

            $jnsRaw = strtolower(trim((string) ($r[$cJns] ?? $payRaw)));
            $jns = (str_contains($jnsRaw, 'non') || str_contains($jnsRaw, 'transfer') || str_contains($jnsRaw, 'qris') || $jnsRaw === '2') ? 2 : 1;

            $usr = substr(trim((string) ($r[$cUser] ?? 'admin')) ?: 'admin', 0, 10);

            ACR::updateOrCreate(
                ['REF_NO' => $refNo],
                [
                    'DT'      => $dt,
                    'CST'     => $cstObj->CST,
                    'SLS'     => $slsObj->SLS,
                    'BRT'     => $brt,
                    'PAY'     => $pay,
                    'TOT_POT' => $totPot,
                    'JNS'     => $jns,
                    'POT_1'   => $pot1,
                    'POT_2'   => $pot2,
                    'POT_R'   => $potR,
                    'USER'    => $usr,
                ]
            );
            $count++;
        }

        return $count;
    }

    private function processStsRows(array $rows): int
    {
        if (count($rows) < 2) return 0;
        $header = array_map(fn($h) => strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $h)), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $findCol = function (array $candidates, int $default) use ($header): int {
            foreach ($header as $idx => $h) {
                if (in_array($h, $candidates, true)) return $idx;
            }
            return $default;
        };

        $cRec  = $findCol(['nomorrekam', 'rec', 'id'], 0);
        $cRef  = $findCol(['nomorreferensi', 'refno', 'noref'], 1);
        $cBc   = $findCol(['barcodekeluar', 'bcout', 'barcode'], 2);
        $cItm  = $findCol(['item', 'itm', 'barang', 'namabarang', 'kodeitem'], 3);
        $cHgj  = $findCol(['hargajual', 'hgj', 'harga'], 4);
        $cSt   = $findCol(['stok', 'st', 'satuan', 'qty'], 5);
        $cPot1 = $findCol(['potonganpersen1', 'pot1'], 6);
        $cPot2 = $findCol(['potonganpersen2', 'pot2'], 7);
        $cPotR = $findCol(['potongan', 'potr'], 8);
        $cPpn  = $findCol(['ppn', 'pajak'], 9);
        $cBrt  = $findCol(['berat', 'brt', 'subtotal'], 10);

        $count = 0;
        foreach ($dataRows as $r) {
            $refRaw = trim((string) ($r[$cRef] ?? ''));
            $itmRaw = trim((string) ($r[$cItm] ?? ''));
            if ($refRaw === '' && $itmRaw === '') continue;

            $hgj  = max(0, SimpleExcelReader::parseNumber($r[$cHgj] ?? 0));
            $stok = max(0, (int) round(SimpleExcelReader::parseNumber($r[$cSt] ?? 1)));
            $itmObj = $this->resolveItm($itmRaw ?: 'ITEM', $hgj, $stok);

            // REC di tabel sts bertipe string(3) PK
            $recRaw = trim((string) ($r[$cRec] ?? ''));
            $recKey = $this->normalizeRecKey($recRaw, $count + 1);

            $bcOut = substr(trim((string) ($r[$cBc] ?? '')) ?: $itmObj->ITM, 0, 20);
            $stVal = substr(trim((string) ($r[$cSt] ?? '1')) ?: '1', 0, 3);
            $pot1  = (int) round(SimpleExcelReader::parseNumber($r[$cPot1] ?? 0));
            $pot2  = (int) round(SimpleExcelReader::parseNumber($r[$cPot2] ?? 0));
            $potR  = max(0, SimpleExcelReader::parseNumber($r[$cPotR] ?? 0));
            $ppn   = (int) round(SimpleExcelReader::parseNumber($r[$cPpn] ?? 0));
            $brt   = max(0, SimpleExcelReader::parseNumber($r[$cBrt] ?? $hgj));

            STS::updateOrCreate(
                ['REC' => $recKey],
                [
                    'REF_NO' => strtoupper(substr($refRaw, 0, 10)),
                    'BC_OUT' => $bcOut,
                    'ITM'    => $itmObj->ITM,
                    'HGJ'    => $hgj,
                    'ST'     => $stVal,
                    'POT_1'  => $pot1,
                    'POT_2'  => $pot2,
                    'POT_R'  => $potR,
                    'PPN'    => $ppn,
                    'BRT'    => $brt,
                ]
            );
            $count++;
        }

        return $count;
    }

    private function normalizeRecKey(string $raw, int $seq): string
    {
        $clean = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $raw));
        if (strlen($clean) > 0 && strlen($clean) <= 3 && !STS::where('REC', $clean)->exists()) {
            return $clean;
        }
        // Jika seperti "REC001", ambil 3 digit/karakter terakhir
        if (strlen($clean) > 3) {
            $tail = substr($clean, -3);
            if (!STS::where('REC', $tail)->exists()) {
                return $tail;
            }
        }
        // Fallback: generate 3-char base36 unik
        $n = max($seq, STS::count() + 1);
        do {
            $candidate = strtoupper(str_pad(base_convert((string) $n, 10, 36), 3, '0', STR_PAD_LEFT));
            $candidate = substr($candidate, -3);
            $n++;
        } while (STS::where('REC', $candidate)->exists());

        return $candidate;
    }

    // =========================================================================
    // LOGIKA IMPORT PAKET 2B: STOK BARANG (ITM & ITMDT)
    // Mendukung 2-Sheet (template_data_barang.xlsx) & 1-Sheet (data_barang_lengkap / Saldo)
    // =========================================================================
    private function importPaketStokBarangXls(array $sheets): array
    {
        $sheetBarangMasuk = null;
        $sheetDataBarang  = null;

        foreach ($sheets as $name => $rows) {
            $lower = strtolower(trim($name));
            if (str_contains($lower, 'masuk') || $lower === 'itmdt') {
                $sheetBarangMasuk = $rows;
            } elseif ($lower === 'data barang' || $lower === 'itm' || $lower === 'barang') {
                $sheetDataBarang = $rows;
            }
        }

        // Cek apakah ini format 2-Sheet seperti template_data_barang.xlsx
        if ($sheetBarangMasuk !== null && $sheetDataBarang !== null) {
            $barcodes = [];
            $itmCount = 0;

            DB::transaction(function () use ($sheetDataBarang, $sheetBarangMasuk, &$itmCount, &$barcodes) {
                $itmCount = $this->processItmRows($sheetDataBarang);
                $barcodes = $this->processItmdtRows($sheetBarangMasuk);
            });

            return [
                'total_barang' => $itmCount,
                'total_stok'   => count($barcodes),
                'barcodes'     => $barcodes,
            ];
        }

        // Jika 1 Sheet: cek apakah berisi kolom ITMDT murni ("Barcode Masuk" / "Barcode Keluar")
        $firstSheet = reset($sheets) ?: [];
        $firstHeader = isset($firstSheet[0]) ? strtolower(implode(' ', $firstSheet[0])) : '';
        if (str_contains($firstHeader, 'barcode masuk') || str_contains($firstHeader, 'bc_in')) {
            $barcodes = [];
            DB::transaction(function () use ($firstSheet, &$barcodes) {
                $barcodes = $this->processItmdtRows($firstSheet);
            });
            return [
                'total_barang' => count(array_unique(array_column($barcodes, 'itm'))),
                'total_stok'   => count($barcodes),
                'barcodes'     => $barcodes,
            ];
        }

        // Format 1-Sheet Lengkap (data_barang_lengkap (1).xlsx atau format Saldo)
        return $this->processCombinedItemAndStockRows($firstSheet);
    }

    /**
     * Memproses 1-Sheet gabungan (Kode Barang, Nama Barang, Supplier, Merk, Kategori, Stok/Saldo, Harga Beli, Tanggal Beli, Gudang)
     */
    private function processCombinedItemAndStockRows(array $rows): array
    {
        $headerRowIdx = 0;
        foreach ($rows as $i => $row) {
            $rowText = strtolower(implode(' ', $row));
            if (
                (str_contains($rowText, 'kd') && str_contains($rowText, 'barang')) ||
                (str_contains($rowText, 'kode') && str_contains($rowText, 'barang')) ||
                (str_contains($rowText, 'nama') && str_contains($rowText, 'barang')) ||
                str_contains($rowText, 'saldo') ||
                str_contains($rowText, 'stok')
            ) {
                $headerRowIdx = $i;
                break;
            }
        }

        $headerRow = $rows[$headerRowIdx] ?? [];
        $dataRows  = array_slice($rows, $headerRowIdx + 1);
        $colMap    = SimpleExcelReader::detectColumns($headerRow);

        $colKd      = $colMap['kd_barang'];
        $colNama    = $colMap['nama_barang'];
        $colSupp    = $colMap['supplier'];
        $colMerk    = $colMap['merk'];
        $colKtg     = $colMap['kategori'];
        $colSaldo   = $colMap['saldo'];
        $colHrgBeli = $colMap['harga_beli'];
        $colHrgJual = $colMap['harga_jual'];
        $colTglBeli = $colMap['tanggal_beli'];
        $colGudang  = $colMap['gudang'];

        $importedBarcodes = [];
        $totalBarang = 0;
        $totalStok   = 0;
        $usedBarcodes = [];

        DB::transaction(function () use (
            $dataRows, $colKd, $colNama, $colSupp, $colMerk, $colKtg,
            $colSaldo, $colHrgBeli, $colHrgJual, $colTglBeli, $colGudang,
            &$importedBarcodes, &$totalBarang, &$totalStok, &$usedBarcodes
        ) {
            foreach ($dataRows as $row) {
                $kdRaw   = trim((string) ($row[$colKd] ?? ''));
                $namaRaw = trim((string) ($row[$colNama] ?? ''));

                if ($kdRaw === '' && $namaRaw === '') {
                    continue;
                }

                $saldo   = max(0, (int) round(SimpleExcelReader::parseNumber($row[$colSaldo] ?? 0)));
                $hrgBeli = max(0.0, SimpleExcelReader::parseNumber($row[$colHrgBeli] ?? 0));
                $hrgJual = ($colHrgJual !== null) ? max(0.0, SimpleExcelReader::parseNumber($row[$colHrgJual] ?? 0)) : 0.0;

                // Resolusi Supplier, Merk, Kategori, dan Gudang (cek nama di DB -> ambil kode, jika belum ada -> buat baru)
                $suppVal = ($colSupp !== null) ? trim((string) ($row[$colSupp] ?? '-')) : '-';
                $merkVal = ($colMerk !== null) ? trim((string) ($row[$colMerk] ?? '-')) : '-';
                $ktgVal  = ($colKtg !== null)  ? trim((string) ($row[$colKtg] ?? '-'))  : '-';
                $gdVal   = ($colGudang !== null) ? trim((string) ($row[$colGudang] ?? '')) : '';

                $spp = $this->resolveSpp($suppVal ?: '-');
                $mrk = $this->resolveMrk($merkVal ?: '-');
                $ktg = $this->resolveKtg($ktgVal ?: '-');
                $gd  = $this->resolveGd($gdVal);

                // Tanggal masuk: jika ada kolom Tanggal Beli di template data_barang_lengkap dan bukan format lama
                $tmDate = now()->toDateString();
                if ($colTglBeli !== null && $colSupp !== null && !empty($row[$colTglBeli])) {
                    $tmDate = SimpleExcelReader::parseDate($row[$colTglBeli], now()->toDateString());
                }

                $kdBarang = strtoupper(substr($kdRaw, 0, 10));
                if ($kdBarang === '') {
                    $kdBarang = $this->generateUniqueCode($namaRaw, 'itm', 'ITM', 'ITM');
                }
                $namaBarang = substr($namaRaw ?: $kdBarang, 0, 150);

                $itm = ITM::where('ITM', $kdBarang)->first();
                if ($itm) {
                    $itm->STOK = (int) $itm->STOK + $saldo;
                    if ($colHrgJual !== null) $itm->HRG = $hrgJual;
                    if ($colMerk !== null && $merkVal !== '') $itm->MRK = $mrk->MRK;
                    if ($colKtg !== null && $ktgVal !== '')   $itm->KTG = $ktg->KTG;
                    $itm->save();
                } else {
                    $desToUse = $namaBarang;
                    if (ITM::where('DES', $desToUse)->where('MRK', $mrk->MRK)->where('KTG', $ktg->KTG)->exists()) {
                        $desToUse = substr($desToUse, 0, 138) . ' (' . $kdBarang . ')';
                    }

                    $itm = ITM::create([
                        'ITM'  => $kdBarang,
                        'DES'  => $desToUse,
                        'STOK' => $saldo,
                        'HRG'  => $hrgJual,
                        'MRK'  => $mrk->MRK,
                        'KTG'  => $ktg->KTG,
                    ]);
                }

                $totalBarang++;
                $totalStok += $saldo;

                for ($s = 1; $s <= $saldo; $s++) {
                    $bcOut = $this->generateBcOut($itm->ITM, $usedBarcodes);

                    ITMDT::create([
                        'ITM'    => $itm->ITM,
                        'BC_IN'  => $bcOut,
                        'BC_OUT' => $bcOut,
                        'TM'     => $tmDate,
                        'HRB'    => $hrgBeli,
                        'GD'     => $gd->GD,
                        'SPP'    => $spp->SPP,
                    ]);

                    $importedBarcodes[] = [
                        'itm'    => $itm->ITM,
                        'des'    => $itm->DES,
                        'gd'     => $gd->DES ?? '-',
                        'spp'    => $spp->DES ?? '-',
                        'bc_in'  => $bcOut,
                        'bc_out' => $bcOut,
                        'hrb'    => $hrgBeli,
                    ];
                }
            }
        });

        return [
            'total_barang' => $totalBarang,
            'total_stok'   => $totalStok,
            'barcodes'     => $importedBarcodes,
        ];
    }

    // =========================================================================
    // LOGIKA IMPORT PAKET 2C: DATA BARANG (data_barang_lengkap (1).xlsx / ITM)
    // =========================================================================
    private function importPaketBarangXls(array $sheets): array
    {
        // Jika file berisi 2 sheet (Data Barang Masuk & Data Barang seperti template_data_barang.xlsx)
        $hasMasukSheet = false;
        $targetSheet = null;
        foreach ($sheets as $name => $rows) {
            $lower = strtolower(trim($name));
            if (str_contains($lower, 'masuk') || $lower === 'itmdt') {
                $hasMasukSheet = true;
            } elseif ($lower === 'data barang' || $lower === 'itm' || $lower === 'barang') {
                $targetSheet = $rows;
            }
        }
        if ($targetSheet === null) {
            $targetSheet = reset($sheets) ?: [];
        }

        // Cek apakah sheet memiliki kolom format data_barang_lengkap (1).xlsx atau format Saldo
        $headerNorm = isset($targetSheet[0])
            ? array_map(fn($h) => strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $h)), $targetSheet[0])
            : [];
        $headerJoined = implode(' ', $headerNorm);

        if (
            str_contains($headerJoined, 'hargabeli') ||
            str_contains($headerJoined, 'hrgb') ||
            in_array('supplier', $headerNorm, true) ||
            in_array('namasuppl', $headerNorm, true) ||
            in_array('tanggalbeli', $headerNorm, true) ||
            in_array('gudang', $headerNorm, true) ||
            in_array('saldo', $headerNorm, true)
        ) {
            return $this->processCombinedItemAndStockRows($targetSheet);
        }

        if ($hasMasukSheet && count($sheets) >= 2) {
            return $this->importPaketStokBarangXls($sheets);
        }

        $count = 0;
        DB::transaction(function () use ($targetSheet, &$count) {
            $count = $this->processItmRows($targetSheet);
        });

        return ['itm' => $count];
    }

    private function processItmRows(array $rows): int
    {
        if (count($rows) < 2) return 0;
        $header = array_map(fn($h) => strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $h)), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $hasHeader = false;
        foreach ($header as $h) {
            if (in_array($h, ['kodeitem', 'kodebarang', 'kdbarang', 'itm', 'cd', 'nama', 'namabarang', 'des', 'stok', 'saldo', 'harga', 'kategori', 'merk'], true)) {
                $hasHeader = true;
                break;
            }
        }

        $findCol = function (array $candidates, ?int $default = null) use ($header, $hasHeader): ?int {
            foreach ($header as $idx => $h) {
                if (in_array($h, $candidates, true)) return $idx;
            }
            return $hasHeader ? null : $default;
        };

        $cKd   = $findCol(['kodeitem', 'kodebarang', 'kdbarang', 'itm', 'cd'], 0) ?? 0;
        $cNama = $findCol(['nama', 'namabarang', 'des', 'deskripsi'], 1) ?? 1;
        $cStok = $findCol(['stok', 'saldo', 'stock', 'qty'], 2);
        $cHrg  = $findCol(['harga', 'hargajual', 'hrg'], 3);
        $cKtg  = $findCol(['kategori', 'ktg'], 4);
        $cMrk  = $findCol(['merk', 'merek', 'mrk'], 5);

        $count = 0;
        foreach ($dataRows as $r) {
            $kdRaw   = ($cKd !== null) ? trim((string) ($r[$cKd] ?? '')) : '';
            $namaRaw = ($cNama !== null) ? trim((string) ($r[$cNama] ?? '')) : '';
            if ($kdRaw === '' && $namaRaw === '') continue;

            $kd   = strtoupper(substr($kdRaw, 0, 10)) ?: $this->generateUniqueCode($namaRaw, 'itm', 'ITM', 'ITM');
            $nama = substr($namaRaw ?: $kd, 0, 150);
            $stok = ($cStok !== null) ? max(0, (int) round(SimpleExcelReader::parseNumber($r[$cStok] ?? 0))) : 0;
            $hrg  = ($cHrg !== null)  ? max(0.0, SimpleExcelReader::parseNumber($r[$cHrg] ?? 0)) : 0.0;

            $ktgVal = ($cKtg !== null) ? trim((string) ($r[$cKtg] ?? '-')) : '-';
            $mrkVal = ($cMrk !== null) ? trim((string) ($r[$cMrk] ?? '-')) : '-';

            $ktg = $this->resolveKtg($ktgVal ?: '-');
            $mrk = $this->resolveMrk($mrkVal ?: '-');

            $itm = ITM::where('ITM', $kd)->first();
            if ($itm) {
                $itm->update([
                    'DES'  => $nama,
                    'STOK' => $stok,
                    'HRG'  => $hrg,
                    'KTG'  => $ktg->KTG,
                    'MRK'  => $mrk->MRK,
                ]);
            } else {
                $desToUse = $nama;
                if (ITM::where('DES', $desToUse)->where('MRK', $mrk->MRK)->where('KTG', $ktg->KTG)->exists()) {
                    $desToUse = substr($desToUse, 0, 138) . ' (' . $kd . ')';
                }
                ITM::create([
                    'ITM'  => $kd,
                    'DES'  => $desToUse,
                    'STOK' => $stok,
                    'HRG'  => $hrg,
                    'KTG'  => $ktg->KTG,
                    'MRK'  => $mrk->MRK,
                ]);
            }
            $count++;
        }

        return $count;
    }

    private function processItmdtRows(array $rows): array
    {
        if (count($rows) < 2) return [];
        $header = array_map(fn($h) => strtolower(preg_replace('/[^a-z0-9]/i', '', (string) $h)), $rows[0]);
        $dataRows = array_slice($rows, 1);

        $findCol = function (array $candidates, ?int $default = null) use ($header): ?int {
            foreach ($header as $idx => $h) {
                if (in_array($h, $candidates, true)) return $idx;
            }
            return $default;
        };

        $cItm   = $findCol(['kodeitem', 'kodebarang', 'itm', 'item'], 0);
        $cBcIn  = $findCol(['barcodemasuk', 'bcin'], 1);
        $cBcOut = $findCol(['barcodekeluar', 'bcout', 'barcode'], 2);
        $cTm    = $findCol(['tanggalmasuk', 'tm', 'tanggalbeli', 'tanggal'], 3);
        $cHrb   = $findCol(['hargabeli', 'hrb', 'harga'], 4);
        $cGd    = $findCol(['gudang', 'gd'], 5);
        $cSpp   = $findCol(['supplier', 'spp'], null);

        $barcodes = [];
        $usedBarcodes = [];

        foreach ($dataRows as $r) {
            $itmRaw = ($cItm !== null) ? trim((string) ($r[$cItm] ?? '')) : '';
            if ($itmRaw === '') continue;

            $hrb   = ($cHrb !== null) ? max(0.0, SimpleExcelReader::parseNumber($r[$cHrb] ?? 0)) : 0.0;
            $tm    = ($cTm !== null) ? SimpleExcelReader::parseDate($r[$cTm] ?? null) : now()->toDateString();
            $gdVal = ($cGd !== null) ? trim((string) ($r[$cGd] ?? '')) : '';
            $spVal = ($cSpp !== null) ? trim((string) ($r[$cSpp] ?? '-')) : '-';

            $itmObj = $this->resolveItm($itmRaw, 0, 1);
            $gdObj  = $this->resolveGd($gdVal);
            $sppObj = $this->resolveSpp($spVal ?: '-');

            $bcOutRaw = ($cBcOut !== null) ? trim((string) ($r[$cBcOut] ?? '')) : '';
            $bcOut    = $bcOutRaw !== '' ? substr($bcOutRaw, 0, 20) : $this->generateBcOut($itmObj->ITM, $usedBarcodes);
            $bcInRaw  = ($cBcIn !== null) ? trim((string) ($r[$cBcIn] ?? '')) : '';
            $bcIn     = $bcInRaw !== '' ? substr($bcInRaw, 0, 20) : $bcOut;

            ITMDT::updateOrCreate(
                ['BC_OUT' => $bcOut],
                [
                    'ITM'   => $itmObj->ITM,
                    'BC_IN' => $bcIn,
                    'TM'    => $tm,
                    'HRB'   => $hrb,
                    'GD'    => $gdObj->GD,
                    'SPP'   => $sppObj->SPP,
                ]
            );

            $barcodes[] = [
                'itm'    => $itmObj->ITM,
                'des'    => $itmObj->DES,
                'gd'     => $gdObj->DES ?? '-',
                'spp'    => $sppObj->DES ?? '-',
                'bc_in'  => $bcIn,
                'bc_out' => $bcOut,
                'hrb'    => $hrb,
            ];
        }

        return $barcodes;
    }

    // =========================================================================
    // LOGIKA IMPORT TIPE 1 & 3: PER TABEL & FULL BACKUP XLS
    // =========================================================================
    private function importPerTabelXls(array $sheets, array $selectedTables, bool $isBackup = false): array
    {
        $rowsCount = 0;
        $itemsCount = 0;
        $barcodes = [];

        // Normalisasi map sheet berdasarkan nama lowercase
        $sheetMap = [];
        foreach ($sheets as $sName => $sRows) {
            $sheetMap[strtolower(trim($sName))] = $sRows;
        }
        $firstSheet = reset($sheets) ?: [];

        DB::transaction(function () use (
            $selectedTables, $sheetMap, $firstSheet, $sheets, $isBackup,
            &$rowsCount, &$itemsCount, &$barcodes
        ) {
            foreach ($selectedTables as $table) {
                $rows = $sheetMap[$table] ?? null;

                // Cek alias nama sheet umum
                if ($rows === null) {
                    $aliases = match ($table) {
                        'itm'   => ['data barang', 'barang', 'master barang'],
                        'itmdt' => ['data barang masuk', 'stok barang', 'detail barang'],
                        'acr'   => ['penjualan', 'transaksi'],
                        'sts'   => ['detail penjualan'],
                        'mrk'   => ['merk', 'merek'],
                        'ktg'   => ['kategori'],
                        'spp'   => ['supplier'],
                        'gd'    => ['gudang'],
                        'cst'   => ['customer', 'pelanggan'],
                        'sls'   => ['sales'],
                        default => [],
                    };
                    foreach ($aliases as $alias) {
                        if (isset($sheetMap[$alias])) {
                            $rows = $sheetMap[$alias];
                            break;
                        }
                    }
                }

                // Jika mode Per Tabel dan hanya 1 tabel dipilih serta hanya 1 sheet di file
                if ($rows === null && !$isBackup && count($selectedTables) === 1 && count($sheets) === 1) {
                    $rows = $firstSheet;
                }

                if (empty($rows) || count($rows) < 2) {
                    continue;
                }

                if ($table === 'itm') {
                    // Cek apakah format gabungan (memiliki kolom Harga Beli / Saldo untuk ITMDT sekaligus)
                    $hStr = strtolower(implode(' ', $rows[0]));
                    if (str_contains($hStr, 'harga beli') || str_contains($hStr, 'hrg. b')) {
                        $comb = $this->processCombinedItemAndStockRows($rows);
                        $rowsCount += $comb['total_barang'];
                        $itemsCount += $comb['total_barang'];
                        $barcodes = array_merge($barcodes, $comb['barcodes']);
                    } else {
                        $c = $this->processItmRows($rows);
                        $rowsCount += $c;
                        $itemsCount += $c;
                    }
                } elseif ($table === 'itmdt') {
                    $bcs = $this->processItmdtRows($rows);
                    $rowsCount += count($bcs);
                    $barcodes = array_merge($barcodes, $bcs);
                } elseif ($table === 'acr') {
                    $rowsCount += $this->processAcrRows($rows);
                } elseif ($table === 'sts') {
                    $rowsCount += $this->processStsRows($rows);
                } else {
                    $rowsCount += $this->processGenericTableRows($table, $rows);
                }
            }
        });

        return [
            'rows_count'  => $rowsCount,
            'items_count' => $itemsCount,
            'barcodes'    => $barcodes,
        ];
    }

    private function processGenericTableRows(string $table, array $rows): int
    {
        if (!Schema::hasTable($table) || count($rows) < 2) {
            return 0;
        }

        $dbColumns = Schema::getColumnListing($table);
        $colUpperMap = [];
        foreach ($dbColumns as $col) {
            $colUpperMap[strtoupper($col)] = $col;
        }

        $header = $rows[0];
        $dataRows = array_slice($rows, 1);

        // Jika header cocok dengan nama kolom DB
        $matchedCols = [];
        foreach ($header as $idx => $hName) {
            $cleanH = strtoupper(trim((string) $hName));
            if (isset($colUpperMap[$cleanH])) {
                $matchedCols[$idx] = $colUpperMap[$cleanH];
            }
        }

        // Jika tabel master (mrk, ktg, spp, gd, cst, sls) diunggah dengan kolom "Nama" / "Deskripsi"
        if (empty($matchedCols)) {
            $count = 0;
            foreach ($dataRows as $r) {
                $val0 = trim((string) ($r[0] ?? ''));
                $val1 = trim((string) ($r[1] ?? ''));
                $nameVal = $val1 !== '' ? $val1 : $val0;
                if ($nameVal === '') continue;

                match ($table) {
                    'mrk' => $this->resolveMrk($nameVal),
                    'ktg' => $this->resolveKtg($nameVal),
                    'spp' => $this->resolveSpp($nameVal),
                    'gd'  => $this->resolveGd($nameVal),
                    'cst' => $this->resolveCst($nameVal),
                    'sls' => $this->resolveSls($nameVal),
                    default => null,
                };
                $count++;
            }
            return $count;
        }

        $pkMap = [
            'mrk'   => 'MRK',
            'ktg'   => 'KTG',
            'spp'   => 'SPP',
            'gd'    => 'GD',
            'cst'   => 'CST',
            'sls'   => 'SLS',
            'kas'   => 'CD',
            'rp'    => 'RP',
            'rpd'   => 'RPD',
            'users' => 'USERNAME',
        ];
        $pk = $pkMap[$table] ?? $dbColumns[0];

        $count = 0;
        foreach ($dataRows as $r) {
            $record = [];
            foreach ($matchedCols as $colIdx => $colName) {
                $val = $r[$colIdx] ?? null;
                $record[$colName] = ($val === '') ? null : $val;
            }
            if (empty($record) || empty($record[$pk])) {
                continue;
            }
            $pkVal = $record[$pk];
            DB::table($table)->updateOrInsert([$pk => $pkVal], $record);
            $count++;
        }

        return $count;
    }

    // =========================================================================
    // LOGIKA EXPORT XLS & SQL (ZIP)
    // =========================================================================
    private function exportTablesXlsx(array $tables, string $downloadName)
    {
        $sheets = [];
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) continue;

            if ($table === 'itm') {
                $sheets['itm'] = $this->buildDataBarangSheetRows();
                continue;
            }
            if ($table === 'itmdt') {
                $sheets['itmdt'] = $this->buildDataBarangMasukSheetRows();
                continue;
            }
            if ($table === 'acr') {
                $sheets['acr'] = $this->buildPenjualanSheetRows();
                continue;
            }
            if ($table === 'sts') {
                $sheets['sts'] = $this->buildDetailPenjualanSheetRows();
                continue;
            }

            $cols = array_values(array_diff(Schema::getColumnListing($table), ['created_at', 'updated_at']));
            $rows = DB::table($table)->get();

            $sheetRows = [$cols];
            foreach ($rows as $r) {
                $rArr = (array) $r;
                $line = [];
                foreach ($cols as $c) {
                    $line[] = $rArr[$c] ?? '';
                }
                $sheetRows[] = $line;
            }
            $sheets[$table] = $sheetRows;
        }

        $tempPath = SimpleExcelReader::writeSheets($sheets);
        return response()->download($tempPath, $downloadName)->deleteFileAfterSend(true);
    }

    private function exportPaketPenjualanXlsx(string $downloadName)
    {
        $sheetPenjualan = $this->buildPenjualanSheetRows();
        $sheetDetail    = $this->buildDetailPenjualanSheetRows();

        $tempPath = SimpleExcelReader::writeSheets([
            'Penjualan'        => $sheetPenjualan,
            'Detail Penjualan' => $sheetDetail,
        ]);

        return response()->download($tempPath, $downloadName)->deleteFileAfterSend(true);
    }

    private function buildPenjualanSheetRows(): array
    {
        $acrRows = DB::table('acr as a')
            ->leftJoin('cst as c', 'c.CST', '=', 'a.CST')
            ->leftJoin('sls as s', 's.SLS', '=', 'a.SLS')
            ->select('a.*', 'c.DES as cst_nama', 's.DES as sls_nama')
            ->orderBy('a.DT', 'desc')
            ->get();

        $sheetPenjualan = [[
            'Nomor Referensi', 'Tanggal Transaksi', 'Kode Customer', 'Sales',
            'Nilai Bruto', 'Pembayaran', 'Total Potongan', 'Jenis Pembayaran',
            'Potongan persen 1', 'Potongan persen 2', 'Potongan', 'User'
        ]];

        foreach ($acrRows as $a) {
            $sheetPenjualan[] = [
                $a->REF_NO,
                $a->DT,
                $a->cst_nama ?: $a->CST,
                $a->sls_nama ?: $a->SLS,
                (float) $a->BRT,
                (float) $a->PAY,
                (float) $a->TOT_POT,
                ((int) $a->JNS === 2) ? 'Non Tunai' : 'Tunai',
                (int) ($a->POT_1 ?? 0),
                (int) ($a->POT_2 ?? 0),
                (float) ($a->POT_R ?? 0),
                $a->USER,
            ];
        }

        return $sheetPenjualan;
    }

    private function buildDetailPenjualanSheetRows(): array
    {
        $stsRows = DB::table('sts as st')
            ->leftJoin('itm as i', 'i.ITM', '=', 'st.ITM')
            ->select('st.*', 'i.DES as itm_nama')
            ->get();

        $sheetDetail = [[
            'Nomor Rekam', 'Nomor Referensi', 'Barcode Keluar', 'item',
            'Harga Jual', 'Stok', 'Potongan persen 1', 'Potongan persen 2',
            'Potongan', 'PPN', 'Berat'
        ]];

        foreach ($stsRows as $st) {
            $sheetDetail[] = [
                $st->REC,
                $st->REF_NO,
                $st->BC_OUT,
                $st->itm_nama ?: $st->ITM,
                (float) $st->HGJ,
                $st->ST,
                (int) ($st->POT_1 ?? 0),
                (int) ($st->POT_2 ?? 0),
                (float) ($st->POT_R ?? 0),
                (int) ($st->PPN ?? 0),
                (float) $st->BRT,
            ];
        }

        return $sheetDetail;
    }

    private function exportPaketStokBarangXlsx(string $downloadName)
    {
        // Sheet 1: Data Barang Masuk (itmdt) - sesuai template_data_barang.xlsx (6 kolom)
        $sheetMasuk = $this->buildDataBarangMasukSheetRows();

        // Sheet 2: Data Barang (itm) - sesuai template_data_barang.xlsx (6 kolom)
        $sheetBarang = $this->buildDataBarangSheetRows();

        $tempPath = SimpleExcelReader::writeSheets([
            'Data Barang Masuk' => $sheetMasuk,
            'Data Barang'       => $sheetBarang,
        ]);

        return response()->download($tempPath, $downloadName)->deleteFileAfterSend(true);
    }

    private function exportPaketBarangXlsx(string $downloadName)
    {
        // Sheet 1: Data Barang - sesuai data_barang_lengkap (1).xlsx (9 kolom)
        $sheetBarang = $this->buildDataBarangLengkapSheetRows();
        $tempPath = SimpleExcelReader::writeSheets([
            'Data Barang' => $sheetBarang,
        ]);

        return response()->download($tempPath, $downloadName)->deleteFileAfterSend(true);
    }

    private function buildDataBarangMasukSheetRows(): array
    {
        $itmdtRows = DB::table('itmdt as d')
            ->leftJoin('gd as g', 'g.GD', '=', 'd.GD')
            ->select('d.*', 'g.DES as gd_nama')
            ->get();

        $sheetMasuk = [[
            'Kode Item', 'Barcode Masuk', 'Barcode Keluar', 'Tanggal Masuk', 'Harga Beli', 'Gudang'
        ]];
        foreach ($itmdtRows as $d) {
            $sheetMasuk[] = [
                $d->ITM,
                $d->BC_IN,
                $d->BC_OUT,
                $d->TM,
                (float) $d->HRB,
                $d->gd_nama ?: $d->GD,
            ];
        }
        return $sheetMasuk;
    }

    private function buildDataBarangSheetRows(): array
    {
        $itmRows = DB::table('itm as i')
            ->leftJoin('ktg as k', 'k.KTG', '=', 'i.KTG')
            ->leftJoin('mrk as m', 'm.MRK', '=', 'i.MRK')
            ->select('i.*', 'k.DES as ktg_nama', 'm.DES as mrk_nama')
            ->orderBy('i.ITM')
            ->get();

        $sheet = [[
            'Kode Item', 'Nama', 'Stok', 'Harga', 'Kategori', 'Merk'
        ]];
        foreach ($itmRows as $i) {
            $sheet[] = [
                $i->ITM,
                $i->DES,
                (int) $i->STOK,
                (float) $i->HRG,
                $i->ktg_nama ?: $i->KTG,
                $i->mrk_nama ?: $i->MRK,
            ];
        }
        return $sheet;
    }

    private function buildDataBarangLengkapSheetRows(): array
    {
        $itmRows = DB::table('itm as i')
            ->leftJoin('ktg as k', 'k.KTG', '=', 'i.KTG')
            ->leftJoin('mrk as m', 'm.MRK', '=', 'i.MRK')
            ->select('i.*', 'k.DES as ktg_nama', 'm.DES as mrk_nama')
            ->orderBy('i.ITM')
            ->get();

        // Ambil informasi ITMDT terakhir per ITM (Supplier, Harga Beli, Tanggal Beli, Gudang)
        $itmdtMap = [];
        $itmdtRows = DB::table('itmdt as d')
            ->leftJoin('spp as s', 's.SPP', '=', 'd.SPP')
            ->leftJoin('gd as g', 'g.GD', '=', 'd.GD')
            ->select('d.ITM', 'd.HRB', 'd.TM', 's.DES as spp_nama', 'd.SPP', 'g.DES as gd_nama', 'd.GD')
            ->orderBy('d.TM', 'desc')
            ->get();

        foreach ($itmdtRows as $dt) {
            if (!isset($itmdtMap[$dt->ITM])) {
                $itmdtMap[$dt->ITM] = $dt;
            }
        }

        $defaultGd = DB::table('gd')->value('DES') ?: 'Gudang Utama';

        $sheet = [[
            'Kode Barang', 'Nama Barang', 'Supplier', 'Merk', 'Kategori',
            'Stok', 'Harga Beli', 'Tanggal Beli', 'Gudang'
        ]];

        foreach ($itmRows as $i) {
            $dt = $itmdtMap[$i->ITM] ?? null;
            $sppNama = $dt ? ($dt->spp_nama ?: $dt->SPP ?: '-') : '-';
            $hrgBeli = $dt ? (float) $dt->HRB : 0;
            $tglBeli = $dt && $dt->TM ? $dt->TM : date('Y-m-d');
            $gdNama  = $dt ? ($dt->gd_nama ?: $dt->GD ?: $defaultGd) : $defaultGd;

            $sheet[] = [
                $i->ITM,
                $i->DES,
                $sppNama,
                $i->mrk_nama ?: $i->MRK ?: '-',
                $i->ktg_nama ?: $i->KTG ?: '-',
                (int) $i->STOK,
                $hrgBeli,
                $tglBeli,
                $gdNama,
            ];
        }

        return $sheet;
    }

    /**
     * Export tabel ke dalam file .zip yang berisi file .sql per tabel
     */
    private function exportSqlZip(array $tables, string $zipDownloadName, bool $includeCombinedSql = false)
    {
        $tempZipPath = tempnam(sys_get_temp_dir(), 'export_sql_') . '.zip';
        $zip = new ZipArchive();
        if ($zip->open($tempZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat arsip ZIP untuk export SQL.');
        }

        $combinedSql = "-- Full Backup SQL Export\n-- Tanggal: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) continue;
            $sqlDump = $this->generateTableSqlDump($table);
            $zip->addFromString("{$table}.sql", $sqlDump);
            $combinedSql .= $sqlDump . "\n\n";
        }

        $combinedSql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        if ($includeCombinedSql) {
            $zip->addFromString('full_backup.sql', $combinedSql);
        }

        $zip->close();

        return response()->download($tempZipPath, $zipDownloadName)->deleteFileAfterSend(true);
    }

    private function generateTableSqlDump(string $table): string
    {
        $rows = DB::table($table)->get();
        $sql = "-- Dump Data Tabel `{$table}`\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";

        if ($rows->isEmpty()) {
            $sql .= "-- (Tabel `{$table}` kosong)\nSET FOREIGN_KEY_CHECKS=1;\n";
            return $sql;
        }

        $pdo = DB::connection()->getPdo();
        foreach ($rows as $row) {
            $arr = (array) $row;
            $cols = array_map(fn($c) => "`{$c}`", array_keys($arr));
            $vals = array_map(function ($v) use ($pdo) {
                if ($v === null) return 'NULL';
                if (is_int($v) || is_float($v)) return (string) $v;
                return $pdo->quote((string) $v);
            }, array_values($arr));

            $sql .= "REPLACE INTO `{$table}` (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ");\n";
        }

        $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
        return $sql;
    }

    /**
     * Import file .sql atau .zip yang berisi file .sql
     */
    private function importSqlFileOrZip(string $filePath, string $ext, ?array $allowedTables = null): int
    {
        $sqlContents = [];

        if ($ext === 'zip') {
            $zip = new ZipArchive();
            if ($zip->open($filePath) !== true) {
                throw new \RuntimeException('Gagal membuka file arsip .zip.');
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if (!str_ends_with(strtolower($entryName), '.sql')) {
                    continue;
                }
                $baseTable = strtolower(pathinfo($entryName, PATHINFO_FILENAME));
                if ($baseTable === 'full_backup' && $allowedTables !== null) {
                    continue;
                }
                if ($allowedTables !== null && $baseTable !== 'full_backup' && !in_array($baseTable, $allowedTables, true)) {
                    continue;
                }
                $content = $zip->getFromIndex($i);
                if ($content !== false && trim($content) !== '') {
                    $sqlContents[] = $content;
                }
            }

            // Fallback jika zip hanya berisi full_backup.sql atau nama custom
            if (empty($sqlContents)) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    if (str_ends_with(strtolower($entryName), '.sql')) {
                        $content = $zip->getFromIndex($i);
                        if ($content !== false && trim($content) !== '') {
                            $sqlContents[] = $content;
                        }
                    }
                }
            }
            $zip->close();
        } else {
            $content = file_get_contents($filePath);
            if ($content !== false && trim($content) !== '') {
                $sqlContents[] = $content;
            }
        }

        if (empty($sqlContents)) {
            throw new \RuntimeException('Tidak ditemukan statement SQL di dalam file yang diunggah.');
        }

        $executed = 0;
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            foreach ($sqlContents as $sqlBlock) {
                DB::unprepared($sqlBlock);
                $executed++;
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        return $executed;
    }

    // =========================================================================
    // RESOLVER NAMA -> KODE DATABASE (Cek Kode -> Cek Nama DES -> Buat Baru)
    // =========================================================================
    private function resolveMrk(?string $val): MRK
    {
        $clean = trim((string) $val);
        if ($clean === '') $clean = '-';

        $byCode = MRK::where('MRK', $clean)->first();
        if ($byCode) return $byCode;

        $byName = MRK::whereRaw('LOWER(DES) = ?', [strtolower($clean)])->first();
        if ($byName) return $byName;

        $code = ($clean === '-')
            ? $this->ensureUniqueDirectCode('MRK_IMPORT', 'mrk', 'MRK', 'MRK')
            : $this->generateUniqueCode($clean, 'mrk', 'MRK', 'MRK');

        return MRK::create([
            'MRK' => $code,
            'DES' => substr($clean, 0, 45),
        ]);
    }

    private function resolveKtg(?string $val): KTG
    {
        $clean = trim((string) $val);
        if ($clean === '') $clean = '-';

        $byCode = KTG::where('KTG', $clean)->first();
        if ($byCode) return $byCode;

        $byName = KTG::whereRaw('LOWER(DES) = ?', [strtolower($clean)])->first();
        if ($byName) return $byName;

        $code = ($clean === '-')
            ? $this->ensureUniqueDirectCode('KTG_IMPORT', 'ktg', 'KTG', 'KTG')
            : $this->generateUniqueCode($clean, 'ktg', 'KTG', 'KTG');

        return KTG::create([
            'KTG' => $code,
            'DES' => substr($clean, 0, 45),
        ]);
    }

    private function resolveSpp(?string $val): SPP
    {
        $clean = trim((string) $val);
        if ($clean === '') $clean = '-';

        $byCode = SPP::where('SPP', $clean)->first();
        if ($byCode) return $byCode;

        $byName = SPP::whereRaw('LOWER(DES) = ?', [strtolower($clean)])->first();
        if ($byName) return $byName;

        $code = ($clean === '-')
            ? $this->ensureUniqueDirectCode('SPP_IMPORT', 'spp', 'SPP', 'SPP')
            : $this->generateUniqueCode($clean, 'spp', 'SPP', 'SPP');

        $tlp = '0';
        while (SPP::where('TLP', $tlp)->exists()) {
            $tlp = '08' . mt_rand(10000000, 99999999);
        }

        return SPP::create([
            'SPP' => $code,
            'DES' => substr($clean, 0, 150),
            'TLP' => $tlp,
        ]);
    }

    private function resolveGd(?string $val): GD
    {
        $clean = trim((string) $val);
        if ($clean === '' || $clean === '-') {
            $existing = GD::first();
            if ($existing) return $existing;
            return GD::create(['GD' => 'GD00000001', 'DES' => 'Gudang Utama']);
        }

        $byCode = GD::where('GD', $clean)->first();
        if ($byCode) return $byCode;

        $byName = GD::whereRaw('LOWER(DES) = ?', [strtolower($clean)])->first();
        if ($byName) return $byName;

        $code = $this->generateUniqueCode($clean, 'gd', 'GD', 'GD');
        return GD::create([
            'GD'  => $code,
            'DES' => substr($clean, 0, 50),
        ]);
    }

    private function resolveCst(?string $val): CST
    {
        $clean = trim((string) $val);
        if ($clean === '') $clean = '-';

        $byCode = CST::where('CST', $clean)->first();
        if ($byCode) return $byCode;

        $byName = CST::whereRaw('LOWER(DES) = ?', [strtolower($clean)])->first();
        if ($byName) return $byName;

        $code = $this->generateUniqueCode($clean, 'cst', 'CST', 'CST');
        $tlp = '08' . mt_rand(10000000, 99999999);

        return CST::create([
            'CST' => $code,
            'DES' => substr($clean, 0, 150),
            'AL'  => '-',
            'TLP' => $tlp,
            'DS'  => null,
        ]);
    }

    private function resolveSls(?string $val): SLS
    {
        $clean = trim((string) $val);
        if ($clean === '') $clean = '-';

        $byCode = SLS::where('SLS', $clean)->first();
        if ($byCode) return $byCode;

        $byName = SLS::whereRaw('LOWER(DES) = ?', [strtolower($clean)])->first();
        if ($byName) return $byName;

        $code = $this->generateUniqueCode($clean, 'sls', 'SLS', 'SLS');
        $hp = '08' . mt_rand(10000000, 99999999);

        return SLS::create([
            'SLS' => $code,
            'DES' => substr($clean, 0, 150),
            'HP'  => $hp,
            'AL'  => '-',
        ]);
    }

    private function resolveItm(?string $val, float $defaultHrg = 0.0, int $defaultStok = 0): ITM
    {
        $clean = trim((string) $val);
        if ($clean === '') $clean = 'ITEM';

        $byCode = ITM::where('ITM', $clean)->first();
        if ($byCode) return $byCode;

        $byName = ITM::whereRaw('LOWER(DES) = ?', [strtolower($clean)])->first();
        if ($byName) return $byName;

        $code = $this->generateUniqueCode($clean, 'itm', 'ITM', 'ITM');
        $mrk = $this->resolveMrk('-');
        $ktg = $this->resolveKtg('-');

        return ITM::create([
            'ITM'  => $code,
            'DES'  => substr($clean, 0, 150),
            'STOK' => $defaultStok,
            'HRG'  => $defaultHrg,
            'MRK'  => $mrk->MRK,
            'KTG'  => $ktg->KTG,
        ]);
    }

    private function ensureUniqueDirectCode(string $preferred, string $table, string $pkCol, string $prefix): string
    {
        if (!DB::table($table)->where($pkCol, $preferred)->exists()) {
            return $preferred;
        }
        return $this->generateUniqueCode($prefix, $table, $pkCol, $prefix);
    }

    private function generateUniqueCode(string $des, string $table, string $pkCol, string $prefix): string
    {
        try {
            $candidate = $this->generateCode($des, strtoupper($table));
            if ($candidate !== '' && strlen($candidate) <= 10 && !DB::table($table)->where($pkCol, $candidate)->exists()) {
                return $candidate;
            }
        } catch (\Throwable $e) {
            // Fallback ke generator sekuensial
        }

        $p = strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $prefix), 0, 3)) ?: 'COD';
        $seq = DB::table($table)->count() + 1;
        do {
            $code = $p . str_pad((string) $seq, 10 - strlen($p), '0', STR_PAD_LEFT);
            $code = substr($code, 0, 10);
            $seq++;
        } while (DB::table($table)->where($pkCol, $code)->exists());

        return $code;
    }

    /**
     * Tampilkan halaman cetak barcode Zebra ZD220 (format sama persis dengan Inventory / Cek Stok).
     */
    public function cetakBarcode()
    {
        $cetak = session('imported_barcodes', []);

        if (empty($cetak)) {
            return redirect()->route('import.xls')->with('warning', 'Tidak ada data barcode hasil import yang dapat dicetak.');
        }

        return view('import.cetak-barcode', compact('cetak'));
    }

    /**
     * Reset / kosongkan daftar barcode hasil import dari session.
     */
    public function reset()
    {
        session()->forget(['imported_barcodes', 'import_summary']);
        return redirect()->route('import.xls')->with('success', 'Daftar barcode hasil import telah dikosongkan.');
    }

    /**
     * BC_OUT = ITM (maks 10) + yymmdd (6) + urutan 4 digit => maks 20 karakter.
     */
    private function generateBcOut(string $kdBarang, array &$usedInBatch = []): string
    {
        $prefix = strtoupper(substr(trim($kdBarang), 0, 10)) . date('ymd');

        static $prefixSequences = [];
        if (!isset($prefixSequences[$prefix])) {
            $prefixSequences[$prefix] = ITMDT::where('BC_OUT', 'like', $prefix . '%')->count();
        }

        do {
            $prefixSequences[$prefix]++;
            $code = $prefix . str_pad((string) $prefixSequences[$prefix], 4, '0', STR_PAD_LEFT);
        } while (isset($usedInBatch[$code]) || ITMDT::where('BC_OUT', $code)->exists());

        $usedInBatch[$code] = true;

        return $code;
    }
}
