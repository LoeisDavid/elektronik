@extends('layouts.app')

@section('title', 'Import & Export Database')

@section('header_title')
    Import & Export Data (XLS / SQL)
@endsection

@section('content')
<div class="flex-1 overflow-y-auto p-6 bg-slate-50">
    <div class="max-w-6xl mx-auto space-y-6">

        {{-- Flash / Validation Errors --}}
        @if ($errors->any())
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="flex-1">
                    <p class="font-bold text-sm">Terjadi Kesalahan:</p>
                    <ul class="mt-1 list-disc list-inside text-xs space-y-0.5 text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Hasil Import Terakhir & Tombol Cetak Barcode --}}
        @if (!empty($importedBarcodes) && count($importedBarcodes) > 0)
            <div class="bg-white border-2 border-emerald-500 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">Import Stok Barang Berhasil!</h2>
                            <p class="text-xs text-gray-500">
                                Total <span class="font-bold text-emerald-600">{{ $importSummary['total_barang'] ?? count(array_unique(array_column($importedBarcodes, 'itm'))) }}</span> barang &bull; 
                                Total <span class="font-bold text-emerald-600">{{ count($importedBarcodes) }}</span> barcode unit siap dicetak.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <a 
                            href="{{ route('import.xls.cetak') }}" 
                            target="_blank"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md hover:shadow transition cursor-pointer"
                        >
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Barcode ({{ count($importedBarcodes) }} Label)
                        </a>

                        <form action="{{ route('import.xls.reset') }}" method="POST" onsubmit="return confirm('Kosongkan daftar barcode ini?')">
                            @csrf
                            <button 
                                type="submit" 
                                class="p-2.5 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-red-600 transition cursor-pointer"
                                title="Kosongkan Hasil Import"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Preview Tabel Barcode --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Daftar Barcode Unit yang Dihasilkan
                        </span>
                        <span class="text-xs text-gray-400">
                            Menampilkan {{ min(100, count($importedBarcodes)) }} dari {{ count($importedBarcodes) }} barcode
                        </span>
                    </div>

                    <div class="max-h-56 overflow-y-auto border border-gray-200 rounded-xl">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-gray-100 text-gray-700 font-semibold sticky top-0">
                                <tr>
                                    <th class="py-2 px-3 text-center w-12">No</th>
                                    <th class="py-2 px-3">Kode ITM</th>
                                    <th class="py-2 px-3">Nama Barang</th>
                                    <th class="py-2 px-3">Barcode (BC_OUT)</th>
                                    <th class="py-2 px-3">Gudang</th>
                                    <th class="py-2 px-3 text-center">Supplier</th>
                                    <th class="py-2 px-3 text-right">Harga Beli</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white font-medium text-gray-800">
                                @foreach (array_slice($importedBarcodes, 0, 100) as $idx => $bc)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-2 px-3 text-center text-gray-400">{{ $idx + 1 }}</td>
                                        <td class="py-2 px-3 font-mono font-bold text-primary-db">{{ $bc['itm'] }}</td>
                                        <td class="py-2 px-3">{{ $bc['des'] }}</td>
                                        <td class="py-2 px-3 font-mono font-semibold text-indigo-700">{{ $bc['bc_out'] }}</td>
                                        <td class="py-2 px-3 text-gray-600">{{ $bc['gd'] }}</td>
                                        <td class="py-2 px-3 text-center text-gray-500">{{ $bc['spp'] }}</td>
                                        <td class="py-2 px-3 text-right font-mono">Rp {{ number_format($bc['hrb'] ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Navigation Tabs: IMPORT vs EXPORT --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex border-b border-gray-200 bg-gray-50">
                @if($canImport)
                    <button
                        type="button"
                        id="tabBtnImport"
                        onclick="switchMainTab('import')"
                        class="flex-1 py-4 px-6 text-sm font-bold flex items-center justify-center gap-2.5 border-b-2 border-primary-db text-primary-db bg-white transition cursor-pointer"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <span>IMPORT DATA (XLS & SQL)</span>
                        <span class="px-2 py-0.5 text-[10px] rounded-full bg-primary-db text-white uppercase">Khusus adminhunt</span>
                    </button>
                @endif

                <button
                    type="button"
                    id="tabBtnExport"
                    onclick="switchMainTab('export')"
                    class="flex-1 py-4 px-6 text-sm font-bold flex items-center justify-center gap-2.5 border-b-2 {{ $canImport ? 'border-transparent text-gray-500 hover:text-gray-800' : 'border-primary-db text-primary-db bg-white' }} transition cursor-pointer"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>EXPORT DATA (XLS & SQL ZIP)</span>
                    <span class="px-2 py-0.5 text-[10px] rounded-full bg-emerald-100 text-emerald-800 uppercase">Admin & Manager</span>
                </button>
            </div>

            {{-- ========================================================================= --}}
            {{-- PANEL 1: IMPORT DATA (HANYA ADMINHUNT)                                    --}}
            {{-- ========================================================================= --}}
            @if($canImport)
            <div id="panelImport" class="p-6 sm:p-8 space-y-6">
                <form action="{{ route('import.xls.store') }}" method="POST" enctype="multipart/form-data" id="formhunterajasi123ata" class="space-y-6">
                    @csrf

                    {{-- LANGKAH 1: PILIH JENIS FILE (XLS vs SQL) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">
                            1. Pilih Jenis Format File Import
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 border-primary-db bg-indigo-50/30 cursor-pointer transition" id="lblImportJenisXls">
                                <input type="radio" name="jenis" value="xls" checked onchange="updateImportUI()" class="w-4 h-4 text-primary-db focus:ring-primary-db" />
                                <div>
                                    <p class="text-sm font-bold text-primary-db">Jenis 1: Excel / Spreadsheet (.xlsx / .xls / .csv)</p>
                                    <p class="text-xs text-gray-500">Membaca baris & sheet spreadsheet otomatis beserta konversi nama ke kode.</p>
                                </div>
                            </label>

                            <label class="relative flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-white cursor-pointer transition" id="lblImportJenisSql">
                                <input type="radio" name="jenis" value="sql" onchange="updateImportUI()" class="w-4 h-4 text-primary-db focus:ring-primary-db" />
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Jenis 2: Database SQL (.sql / .zip)</p>
                                    <p class="text-xs text-gray-500">Import dari file dump .sql atau arsip .zip yang berisi file .sql per tabel.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- LANGKAH 2: PILIH TIPE IMPORT (3 TIPE) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">
                            2. Pilih Tipe Import (3 Tipe)
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            {{-- Tipe 1: Per Tabel --}}
                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-white cursor-pointer transition" id="lblImportTipePertabel">
                                <input type="radio" name="tipe" value="pertabel" onchange="updateImportUI()" class="mt-1 w-4 h-4 text-primary-db" />
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Tipe 1: Per Tabel</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pilih sendiri tabel apa saja yang ingin di-import (multi-sheet XLS atau ZIP SQL).</p>
                                </div>
                            </label>

                            {{-- Tipe 2: Paket Kategori (Penjualan / Stok Barang / Barang) --}}
                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-primary-db bg-indigo-50/30 cursor-pointer transition" id="lblImportTipePaket">
                                <input type="radio" name="tipe" value="paket" checked onchange="updateImportUI()" class="mt-1 w-4 h-4 text-primary-db" />
                                <div>
                                    <p class="text-sm font-bold text-primary-db">Tipe 2: Paket Kategori</p>
                                    <p class="text-xs text-gray-500 mt-0.5">1) Penjualan (acr & sts), 2) Stok Barang (itm & itmdt), 3) Master Barang (itm).</p>
                                </div>
                            </label>

                            {{-- Tipe 3: Backup Semuanya --}}
                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-white cursor-pointer transition" id="lblImportTipeBackup">
                                <input type="radio" name="tipe" value="backup" onchange="updateImportUI()" class="mt-1 w-4 h-4 text-primary-db" />
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Tipe 3: Backup Semuanya</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pulihkan (Restore) seluruh data dari semua tabel database sekaligus.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- SUB-OPSI TIPE 1: PILIH TABEL (PERTABEL) --}}
                    <div id="importPertabelBox" class="hidden p-5 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700 uppercase">Pilih Tabel yang Akan Di-Import:</span>
                            <div class="flex gap-2">
                                <button type="button" onclick="toggleCheckboxes('import-table-cb', true)" class="text-xs font-semibold text-primary-db hover:underline cursor-pointer">Pilih Semua</button>
                                <span class="text-gray-300">|</span>
                                <button type="button" onclick="toggleCheckboxes('import-table-cb', false)" class="text-xs font-semibold text-gray-500 hover:underline cursor-pointer"> Kosongkan</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                            @foreach ($availableTables as $tblKey => $tblLabel)
                                <label class="flex items-center gap-2 p-2.5 rounded-lg bg-white border border-gray-200 hover:border-primary-db text-xs font-medium text-gray-800 cursor-pointer">
                                    <input type="checkbox" name="tables[]" value="{{ $tblKey }}" class="import-table-cb rounded text-primary-db focus:ring-primary-db" />
                                    <span>{{ $tblLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- SUB-OPSI TIPE 2: PILIH PAKET (PENJUALAN / STOK BARANG / BARANG) --}}
                    <div id="importPaketBox" class="p-5 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <span class="block text-xs font-bold text-gray-700 uppercase">Pilih Kategori Paket Import (3 Pilihan):</span>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <label class="flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-primary-db cursor-pointer" id="lblImportSubStok">
                                <input type="radio" name="paket_sub" value="stok_barang" checked onchange="updateImportUI()" class="mt-0.5 text-primary-db" />
                                <div>
                                    <p class="text-xs font-bold text-primary-db">1. Stok Barang (ITM & ITMDT)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Sesuai <strong>template_data_barang.xlsx</strong> (2 Sheet: Data Barang Masuk & Data Barang) atau 1 Sheet.</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-gray-200 cursor-pointer" id="lblImportSubPenjualan">
                                <input type="radio" name="paket_sub" value="penjualan" onchange="updateImportUI()" class="mt-0.5 text-primary-db" />
                                <div>
                                    <p class="text-xs font-bold text-gray-800">2. Penjualan (ACR & STS)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Sesuai <strong>template_penjualan.xlsx</strong> (2 Sheet: Penjualan & Detail Penjualan).</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-gray-200 cursor-pointer" id="lblImportSubBarang">
                                <input type="radio" name="paket_sub" value="barang" onchange="updateImportUI()" class="mt-0.5 text-primary-db" />
                                <div>
                                    <p class="text-xs font-bold text-gray-800">3. Data Barang Lengkap (ITM)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Sesuai <strong>data_barang_lengkap (1).xlsx</strong> (Kode Barang, Nama Barang, Supplier, Merk, Kategori, Stok, Harga Beli, Tanggal Beli, Gudang).</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- AREA UPLOAD FILE --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            3. Pilih Berkas yang Akan Di-Import
                        </label>
                        <div 
                            id="dropZone"
                            class="relative border-2 border-dashed border-gray-300 hover:border-primary-db rounded-2xl p-8 text-center transition-colors bg-gray-50/50 hover:bg-indigo-50/20 cursor-pointer"
                        >
                            <input 
                                type="file" 
                                name="file" 
                                id="fileInput" 
                                accept=".xlsx,.xls,.csv,.sql,.zip" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                required
                            />

                            <div id="dropContentEmpty" class="space-y-2">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-primary-db mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-800">
                                    <span class="text-primary-db hover:underline">Pilih berkas</span> atau seret ke sini
                                </p>
                                <p id="importFileHint" class="text-xs text-gray-500">Mendukung format .XLSX, .XLS, .CSV, .SQL, atau .ZIP (Maks. 50MB)</p>
                            </div>

                            <div id="dropContentSelected" class="hidden space-y-1.5">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <p id="fileName" class="text-sm font-bold text-gray-900"></p>
                                <p id="fileSize" class="text-xs text-gray-500"></p>
                                <p class="text-xs text-indigo-600 font-medium">Klik untuk mengganti berkas</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <p class="text-xs text-gray-500 italic">
                            * Jika kolom relasi (Merk, Kategori, Supplier, Gudang, Customer, Sales) berisi <strong>Nama</strong>, sistem otomatis mencocokkan nama di DB atau membuat kode baru jika belum ada.
                        </p>
                        <button 
                            type="submit" 
                            id="btnSubmitImport"
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary-db hover:bg-primary-sdb text-white font-bold text-sm shadow-md transition cursor-pointer shrink-0"
                        >
                            <svg id="btnIcon" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            <svg id="btnSpinner" class="hidden animate-spin w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span id="btnText">Simpan & Proses Import</span>
                        </button>
                    </div>
                </form>
            </div>
            @endif

            {{-- ========================================================================= --}}
            {{-- PANEL 2: EXPORT DATA (DAPAT DIAKSES ADMIN, MANAGER & ADMINHUNT)           --}}
            {{-- ========================================================================= --}}
            <div id="panelExport" class="{{ $canImport ? 'hidden' : '' }} p-6 sm:p-8 space-y-6">
                <form action="{{ route('import.xls.export') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- LANGKAH 1: PILIH JENIS EXPORT (XLS vs SQL ZIP) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">
                            1. Pilih Jenis Format File Export
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-emerald-600 bg-emerald-50/30 cursor-pointer transition" id="lblExportJenisXls">
                                <input type="radio" name="jenis" value="xls" checked onchange="updateExportUI()" class="w-4 h-4 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-bold text-emerald-800">Jenis 1: Excel Spreadsheet (.xlsx)</p>
                                    <p class="text-xs text-gray-500">Menghasilkan file spreadsheet .xlsx multi-sheet yang rapi dan siap diedit.</p>
                                </div>
                            </label>

                            <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-white cursor-pointer transition" id="lblExportJenisSql">
                                <input type="radio" name="jenis" value="sql" onchange="updateExportUI()" class="w-4 h-4 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Jenis 2: SQL Archive (.zip berisi .sql)</p>
                                    <p class="text-xs text-gray-500">Menghasilkan bundel .zip yang berisi file dump .sql untuk setiap tabel.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- LANGKAH 2: PILIH TIPE EXPORT (3 TIPE) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2.5">
                            2. Pilih Tipe Export (3 Tipe)
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-white cursor-pointer transition" id="lblExportTipePertabel">
                                <input type="radio" name="tipe" value="pertabel" onchange="updateExportUI()" class="mt-1 w-4 h-4 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Tipe 1: Per Tabel</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Pilih tabel tertentu yang akan di-export (menjadi sheet XLS atau .zip berisi .sql).</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-emerald-600 bg-emerald-50/30 cursor-pointer transition" id="lblExportTipePaket">
                                <input type="radio" name="tipe" value="paket" checked onchange="updateExportUI()" class="mt-1 w-4 h-4 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-bold text-emerald-800">Tipe 2: Paket Kategori</p>
                                    <p class="text-xs text-gray-500 mt-0.5">1) Penjualan (acr & sts), 2) Stok Barang (itm & itmdt), 3) Barang (itm).</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-gray-200 hover:border-gray-300 bg-white cursor-pointer transition" id="lblExportTipeBackup">
                                <input type="radio" name="tipe" value="backup" onchange="updateExportUI()" class="mt-1 w-4 h-4 text-emerald-600" />
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Tipe 3: Backup Semuanya</p>
                                    <p class="text-xs text-gray-500 mt-0.5">Unduh cadangan penuh seluruh tabel di database aktif sekaligus.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- SUB-OPSI EXPORT TIPE 1: PILIH TABEL --}}
                    <div id="exportPertabelBox" class="hidden p-5 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700 uppercase">Pilih Tabel yang Akan Di-Export:</span>
                            <div class="flex gap-2">
                                <button type="button" onclick="toggleCheckboxes('export-table-cb', true)" class="text-xs font-semibold text-emerald-700 hover:underline cursor-pointer">Pilih Semua</button>
                                <span class="text-gray-300">|</span>
                                <button type="button" onclick="toggleCheckboxes('export-table-cb', false)" class="text-xs font-semibold text-gray-500 hover:underline cursor-pointer">Kosongkan</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2.5">
                            @foreach ($availableTables as $tblKey => $tblLabel)
                                <label class="flex items-center gap-2 p-2.5 rounded-lg bg-white border border-gray-200 hover:border-emerald-600 text-xs font-medium text-gray-800 cursor-pointer">
                                    <input type="checkbox" name="tables[]" value="{{ $tblKey }}" checked class="export-table-cb rounded text-emerald-600" />
                                    <span>{{ $tblLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- SUB-OPSI EXPORT TIPE 2: PILIH PAKET --}}
                    <div id="exportPaketBox" class="p-5 rounded-xl bg-slate-50 border border-slate-200 space-y-3">
                        <span class="block text-xs font-bold text-gray-700 uppercase">Pilih Kategori Paket Export (3 Pilihan):</span>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <label class="flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-emerald-600 cursor-pointer" id="lblExportSubStok">
                                <input type="radio" name="paket_sub" value="stok_barang" checked onchange="updateExportUI()" class="mt-0.5 text-emerald-600" />
                                <div>
                                    <p class="text-xs font-bold text-emerald-800">1. Stok Barang (ITM & ITMDT)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Format <strong>template_data_barang.xlsx</strong> (2 Sheet: Data Barang Masuk & Data Barang).</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-gray-200 cursor-pointer" id="lblExportSubPenjualan">
                                <input type="radio" name="paket_sub" value="penjualan" onchange="updateExportUI()" class="mt-0.5 text-emerald-600" />
                                <div>
                                    <p class="text-xs font-bold text-gray-800">2. Penjualan (ACR & STS)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Format <strong>template_penjualan.xlsx</strong> (2 Sheet: Penjualan & Detail Penjualan).</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-gray-200 cursor-pointer" id="lblExportSubBarang">
                                <input type="radio" name="paket_sub" value="barang" onchange="updateExportUI()" class="mt-0.5 text-emerald-600" />
                                <div>
                                    <p class="text-xs font-bold text-gray-800">3. Data Barang Lengkap (ITM)</p>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Format <strong>data_barang_lengkap (1).xlsx</strong> (1 Sheet: Kode Barang, Nama Barang, Supplier, Merk, Kategori, Stok, Harga Beli, Tanggal Beli, Gudang).</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md transition cursor-pointer"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span>Unduh Hasil Export Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Unduh File Template XLS Resmi (database/tamplate) --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-primary-db" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Unduh Template Format Excel Resmi
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <a href="{{ route('import.xls.template', 'data_barang') }}" class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 hover:border-primary-db hover:bg-indigo-50/30 transition">
                    <div>
                        <p class="text-xs font-bold text-primary-db">1. Template Stok Barang (2 Sheet)</p>
                        <p class="text-[11px] text-gray-500">template_data_barang.xlsx (ITMDT & ITM)</p>
                    </div>
                    <span class="text-xs font-bold text-indigo-600">Unduh &darr;</span>
                </a>

                <a href="{{ route('import.xls.template', 'penjualan') }}" class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 hover:border-primary-db hover:bg-indigo-50/30 transition">
                    <div>
                        <p class="text-xs font-bold text-primary-db">2. Template Penjualan (2 Sheet)</p>
                        <p class="text-[11px] text-gray-500">template_penjualan.xlsx (ACR & STS)</p>
                    </div>
                    <span class="text-xs font-bold text-indigo-600">Unduh &darr;</span>
                </a>

                <a href="{{ route('import.xls.template', 'barang_lengkap') }}" class="flex items-center justify-between p-3.5 rounded-xl border border-gray-200 hover:border-primary-db hover:bg-indigo-50/30 transition">
                    <div>
                        <p class="text-xs font-bold text-primary-db">3. Template Data Barang Lengkap (1 Sheet)</p>
                        <p class="text-[11px] text-gray-500">data_barang_lengkap (1).xlsx (9 Kolom)</p>
                    </div>
                    <span class="text-xs font-bold text-indigo-600">Unduh &darr;</span>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection

@section('js')
<script>
    function switchMainTab(tab) {
        const panelImport = document.getElementById('panelImport');
        const panelExport = document.getElementById('panelExport');
        const btnImport   = document.getElementById('tabBtnImport');
        const btnExport   = document.getElementById('tabBtnExport');

        if (tab === 'import' && panelImport) {
            panelImport.classList.remove('hidden');
            panelExport.classList.add('hidden');
            btnImport.classList.add('border-primary-db', 'text-primary-db', 'bg-white');
            btnImport.classList.remove('border-transparent', 'text-gray-500');
            btnExport.classList.remove('border-primary-db', 'text-primary-db', 'bg-white');
            btnExport.classList.add('border-transparent', 'text-gray-500');
        } else {
            if (panelImport) panelImport.classList.add('hidden');
            panelExport.classList.remove('hidden');
            btnExport.classList.add('border-primary-db', 'text-primary-db', 'bg-white');
            btnExport.classList.remove('border-transparent', 'text-gray-500');
            if (btnImport) {
                btnImport.classList.remove('border-primary-db', 'text-primary-db', 'bg-white');
                btnImport.classList.add('border-transparent', 'text-gray-500');
            }
        }
    }

    function toggleCheckboxes(className, checked) {
        document.querySelectorAll('.' + className).forEach(cb => {
            cb.checked = checked;
        });
    }

    function updateImportUI() {
        const form = document.getElementById('formhunterajasi123ata');
        if (!form) return;

        const jenis = form.querySelector('input[name="jenis"]:checked')?.value || 'xls';
        const tipe  = form.querySelector('input[name="tipe"]:checked')?.value || 'paket';
        const sub   = form.querySelector('input[name="paket_sub"]:checked')?.value || 'stok_barang';

        document.getElementById('importPertabelBox').classList.toggle('hidden', tipe !== 'pertabel');
        document.getElementById('importPaketBox').classList.toggle('hidden', tipe !== 'paket');

        const fileInput = document.getElementById('fileInput');
        const hint = document.getElementById('importFileHint');
        if (jenis === 'sql') {
            fileInput.setAttribute('accept', '.sql,.zip');
            hint.textContent = 'Mendukung format .SQL atau arsip .ZIP berisi file .sql';
        } else {
            fileInput.setAttribute('accept', '.xlsx,.xls,.csv');
            hint.textContent = 'Mendukung format spreadsheet .XLSX, .XLS, atau .CSV';
        }

        // Highlight active cards
        document.getElementById('lblImportJenisXls').className = jenis === 'xls'
            ? 'relative flex items-center gap-3 p-4 rounded-xl border-2 border-primary-db bg-indigo-50/30 cursor-pointer transition'
            : 'relative flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition';
        document.getElementById('lblImportJenisSql').className = jenis === 'sql'
            ? 'relative flex items-center gap-3 p-4 rounded-xl border-2 border-primary-db bg-indigo-50/30 cursor-pointer transition'
            : 'relative flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition';

        ['Pertabel', 'Paket', 'Backup'].forEach(k => {
            const el = document.getElementById('lblImportTipe' + k);
            if (!el) return;
            const active = tipe === k.toLowerCase();
            el.className = active
                ? 'flex items-start gap-3 p-4 rounded-xl border-2 border-primary-db bg-indigo-50/30 cursor-pointer transition'
                : 'flex items-start gap-3 p-4 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition';
        });

        const subMap = { 'stok_barang': 'Stok', 'penjualan': 'Penjualan', 'barang': 'Barang' };
        Object.entries(subMap).forEach(([val, suffix]) => {
            const el = document.getElementById('lblImportSub' + suffix);
            if (!el) return;
            el.className = (sub === val)
                ? 'flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-primary-db cursor-pointer'
                : 'flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-gray-200 cursor-pointer';
        });
    }

    function updateExportUI() {
        const panel = document.getElementById('panelExport');
        if (!panel) return;

        const jenis = panel.querySelector('input[name="jenis"]:checked')?.value || 'xls';
        const tipe  = panel.querySelector('input[name="tipe"]:checked')?.value || 'paket';
        const sub   = panel.querySelector('input[name="paket_sub"]:checked')?.value || 'stok_barang';

        document.getElementById('exportPertabelBox').classList.toggle('hidden', tipe !== 'pertabel');
        document.getElementById('exportPaketBox').classList.toggle('hidden', tipe !== 'paket');

        document.getElementById('lblExportJenisXls').className = jenis === 'xls'
            ? 'flex items-center gap-3 p-4 rounded-xl border-2 border-emerald-600 bg-emerald-50/30 cursor-pointer transition'
            : 'flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition';
        document.getElementById('lblExportJenisSql').className = jenis === 'sql'
            ? 'flex items-center gap-3 p-4 rounded-xl border-2 border-emerald-600 bg-emerald-50/30 cursor-pointer transition'
            : 'flex items-center gap-3 p-4 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition';

        ['Pertabel', 'Paket', 'Backup'].forEach(k => {
            const el = document.getElementById('lblExportTipe' + k);
            if (!el) return;
            const active = tipe === k.toLowerCase();
            el.className = active
                ? 'flex items-start gap-3 p-4 rounded-xl border-2 border-emerald-600 bg-emerald-50/30 cursor-pointer transition'
                : 'flex items-start gap-3 p-4 rounded-xl border-2 border-gray-200 bg-white cursor-pointer transition';
        });

        const subMap = { 'stok_barang': 'Stok', 'penjualan': 'Penjualan', 'barang': 'Barang' };
        Object.entries(subMap).forEach(([val, suffix]) => {
            const el = document.getElementById('lblExportSub' + suffix);
            if (!el) return;
            el.className = (sub === val)
                ? 'flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-emerald-600 cursor-pointer'
                : 'flex items-start gap-2.5 p-3.5 rounded-xl bg-white border-2 border-gray-200 cursor-pointer';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        const fileInput = document.getElementById('fileInput');
        const dropContentEmpty = document.getElementById('dropContentEmpty');
        const dropContentSelected = document.getElementById('dropContentSelected');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const formImport = document.getElementById('formhunterajasi123ata');

        if (fileInput) {
            fileInput.addEventListener('change', () => {
                if (fileInput.files && fileInput.files[0]) {
                    const file = fileInput.files[0];
                    fileName.textContent = file.name;
                    fileSize.textContent = (file.size / 1024).toFixed(1) + ' KB';
                    dropContentEmpty.classList.add('hidden');
                    dropContentSelected.classList.remove('hidden');
                } else {
                    dropContentEmpty.classList.remove('hidden');
                    dropContentSelected.classList.add('hidden');
                }
            });
        }

        if (formImport) {
            formImport.addEventListener('submit', () => {
                const btnSubmit = document.getElementById('btnSubmitImport');
                const btnIcon = document.getElementById('btnIcon');
                const btnSpinner = document.getElementById('btnSpinner');
                const btnText = document.getElementById('btnText');
                if (btnSubmit) btnSubmit.disabled = true;
                if (btnIcon) btnIcon.classList.add('hidden');
                if (btnSpinner) btnSpinner.classList.remove('hidden');
                if (btnText) btnText.textContent = 'Memproses & Menyimpan...';
            });
        }
    });
</script>
@endsection
