{{-- ===================================================================== --}}
{{-- MODAL-MODAL HALAMAN KASIR (POINT OF SALES)                            --}}
{{-- ===================================================================== --}}

{{-- 1. MODAL SCAN BARCODE (KAMERA & MANUAL) --}}
<div
    id="modalBarcode"
    class="fixed inset-0 bg-black/50 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

    {{-- Card Modal --}}
    <div class="w-full max-w-2xl bg-[#F7F8FC] rounded-2xl p-6 shadow-2xl space-y-4 border border-[#E2E8F0] max-h-[92vh] overflow-y-auto">

        {{-- Header Modal --}}
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-black tracking-tight">Scan Barcode Kamera</h3>
            <button
                type="button"
                onclick="tutupModalBarcode()"
                class="text-gray-400 hover:text-gray-700 text-2xl font-bold leading-none cursor-pointer">
                &times;
            </button>
        </div>

        {{-- KAMERA: live (HTTPS/localhost/HTTP) atau foto --}}
        <div class="w-full">
            @include('inventory.partials.barcode-camera', ['cameraId' => 'kasir'])
        </div>

        {{-- Form Input Manual Kode Barcode --}}
        <form action="{{ route('POS.scan') }}" method="POST" id="formScanKasir">
            @csrf
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    name="BC_OUT"
                    id="inputManualBarcode"
                    inputmode="text"
                    autocomplete="off"
                    autocapitalize="off"
                    spellcheck="false"
                    placeholder="ketik kode barcode manual..."
                    required
                    class="flex-1 bg-white border border-[#2D3748] rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-500 outline-none focus:ring-2 focus:ring-[#101D36]" />

                <button
                    type="submit"
                    class="bg-[#101D36] hover:bg-[#1A2C50] active:bg-black text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors cursor-pointer">
                    Cari
                </button>
            </div>
        </form>

        {{-- Tombol Batal --}}
        <div>
            <button
                type="button"
                onclick="tutupModalBarcode()"
                class="w-full bg-white hover:bg-gray-100 active:bg-gray-200 border border-slate-300 rounded-xl py-2.5 text-center text-sm font-semibold text-black transition-colors cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- 2. MODAL DRAFT TRANSAKSI --}}
<div
    id="modalDraftTransaksi"
    onclick="tutupModalDraftKlikLuar(event)"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

    <!-- Card Modal -->
    <div
        onclick="event.stopPropagation()"
        class="w-full max-w-lg bg-[#F8FAFC] rounded-3xl p-6 shadow-2xl space-y-4 border border-gray-200">

        <!-- Header Modal -->
        <div class="flex items-start justify-between pb-2 border-b border-gray-300">
            <div>
                <h3 class="text-2xl font-extrabold text-black tracking-tight">Draft Transaksi</h3>
                <p id="draftCountText" class="text-xs text-gray-500 font-medium mt-1">0 transaksi tersimpan</p>
            </div>
            <button
                type="button"
                onclick="tutupModalDraftTransaksi()"
                class="w-8 h-8 flex items-center justify-center border border-gray-400 rounded-lg text-black hover:bg-gray-200 transition-colors font-bold text-sm cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Container List Draft (Max Height setara 4 Item, Scrollbar Khusus List) -->
        <div id="listDraftContainer" class="space-y-3 max-h-[360px] overflow-y-auto pr-1">
            <!-- Diisi via JavaScript dari localStorage -->
        </div>

        <!-- Footer Modal -->
        <div class="pt-3 border-t border-gray-300 flex items-center justify-between">
            <span class="text-xs text-gray-400 font-medium">Klik Lanjutkan untuk memuat draft ke keranjang</span>
            <button
                type="button"
                onclick="tutupModalDraftTransaksi()"
                class="px-6 py-2 bg-white border border-gray-800 hover:bg-gray-100 text-black rounded-xl text-sm font-bold transition-colors cursor-pointer">
                Tutup
            </button>
        </div>

    </div>
</div>

{{-- 3. MODAL BUKA KASIR --}}
<div
    id="modalBukaKasir"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
    <div class="w-full max-w-md bg-secondary-bw rounded-2xl p-6 shadow-2xl space-y-4 border border-black">

        <!-- Header: Judul & Tanggal -->
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-black tracking-tight">Pembukaan kasir</h3>
            <span class="text-gray-500 font-medium text-sm">{{ now()->format('d F Y') }}</span>
        </div>

        <!-- Form Input -->
        <div class="space-y-4 pt-2">
            <!-- Kas Opening -->
            <div>
                <label for="kasOpening" class="block text-sm font-semibold text-black mb-1">
                    Kas opening:
                </label>
                <div class="relative">
                    <input
                        type="text"
                        inputmode="numeric"
                        id="kasOpening"
                        placeholder="Rp 0"
                        oninput="formatInputRupiah(this)"
                        class="input-rupiah w-full bg-white border rounded-lg px-3 py-2.5 text-sm text-black outline-none pr-8 font-medium" />
                    <!-- Tombol Clear / X -->
                    <button
                        type="button"
                        onclick="document.getElementById('kasOpening').value = ''"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold hover:text-black">
                        ✕
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3 pt-4">
            <button
                type="button"
                id="btnEksekusiBukaKasir"
                onclick="simpanBukaKasir()"
                class="bg-primary-db hover:bg-primary-sdb text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors cursor-pointer">
                Buka kasir
            </button>
            <button
                type="button"
                onclick="tutupModalBukaKasir()"
                class="bg-white border font-semibold px-5 py-2.5 rounded-lg text-sm transition-colors cursor-pointer">
                Batal
            </button>
        </div>

    </div>
</div>

{{-- 4. MODAL DISKON GLOBAL + VOUCHER --}}
<div
    id="modalDiskonGlobal"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
    <div class="w-full max-w-sm bg-secondary-bw rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">

        <!-- Judul Modal -->
        <h3 class="text-xl font-bold text-black tracking-tight">Diskon &amp; Voucher</h3>

        <!-- Form Input -->
        <div class="space-y-3">
            <!-- Input Diskon (%) -->
            <div>
                <label for="inputDiskon" class="block text-sm font-semibold text-black mb-1">
                    Diskon (%)
                </label>
                <input
                    type="number"
                    id="inputDiskon"
                    min="0"
                    max="100"
                    oninput="if(parseFloat(this.value) > 100) this.value = 100; if(parseFloat(this.value) < 0) this.value = 0;"
                    placeholder="0%"
                    class="w-full bg-white placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium border" />
            </div>

            <!-- Input Voucher (Rp) -->
            <div>
                <label for="inputVoucher" class="block text-sm font-semibold text-black mb-1">
                    Voucher (Rp)
                </label>
                <input
                    type="text"
                    inputmode="numeric"
                    id="inputVoucher"
                    placeholder="Rp 0"
                    oninput="formatInputRupiah(this)"
                    class="input-rupiah w-full bg-white placeholder-gray-400 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium border" />
            </div>
        </div>

        <!-- Tombol Aksi (Batal & Terapkan) -->
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button
                type="button"
                onclick="tutupModalDiskonGlobal()"
                class="w-full border bg-white text-black rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                Batal
            </button>
            <button
                type="button"
                onclick="simpanDiskonGlobal()"
                class="w-full bg-primary-db hover:bg-primary-sdb text-white rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                Terapkan
            </button>
        </div>

    </div>
</div>

{{-- 5. MODAL METODE PEMBAYARAN --}}
<div
    id="modalMetodePembayaran"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

    <!-- Card Modal -->
    <div class="w-full max-w-sm bg-[#F8FAFC] rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">

        <!-- Judul Modal -->
        <h3 class="text-lg font-bold text-black tracking-tight">Metode Pembayaran</h3>

        <!-- Opsi Pembayaran Non Tunai -->
        <div class="space-y-2">
            <span class="block text-sm font-semibold text-black">Non Tunai</span>
            <div class="space-y-2">
                <button
                    type="button"
                    onclick="pilihMetodePembayaran('BNI')"
                    class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                    BNI
                </button>
                <button
                    type="button"
                    onclick="pilihMetodePembayaran('BCA')"
                    class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                    BCA
                </button>
                <button
                    type="button"
                    onclick="pilihMetodePembayaran('BRI')"
                    class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                    BRI
                </button>
                <button
                    type="button"
                    onclick="pilihMetodePembayaran('Seabank')"
                    class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                    Seabank
                </button>
                <button
                    type="button"
                    onclick="pilihMetodePembayaran('JAGO')"
                    class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                    JAGO
                </button>
            </div>
        </div>

        <!-- Opsi Pembayaran Tunai -->
        <div class="space-y-2">
            <span class="block text-sm font-semibold text-black">Tunai</span>
            <button
                type="button"
                onclick="pilihMetodePembayaran('Cash')"
                class="w-full bg-primary-db border border-gray-800 hover:bg-primary-sdb text-white font-medium py-2 rounded-xl text-sm transition-colors cursor-pointer">
                Cash
            </button>
        </div>

        <!-- Tombol Batal -->
        <div class="pt-1">
            <button
                type="button"
                onclick="modalTutupMetodePembayaran()"
                class="w-full bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-black font-semibold py-2 rounded-xl text-sm transition-colors cursor-pointer">
                Batal
            </button>
        </div>

    </div>
</div>

{{-- 6. MODAL SIMPAN DRAFT TRANSAKSI --}}
<div
    id="modalTambahArsip"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

    <!-- Card Modal -->
    <div class="w-full max-w-sm bg-[#F8FAFC] rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">

        <!-- Judul Modal -->
        <h3 class="text-lg font-bold text-black tracking-tight">Simpan sebagai draft?</h3>

        <!-- Form Input -->
        <div class="space-y-4">
            <!-- Input Pelanggan -->
            <div class="space-y-1">
                <label for="inputPelangganDraft" class="block text-sm font-semibold text-black">
                    Pelanggan
                </label>
                <select
                    id="inputPelangganDraft"
                    class="searchable-select w-full bg-white border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                    <option value="">Cari / Pilih Pelanggan</option>
                    @foreach($customers ?? [] as $cst)
                        <option value="{{ $cst->DES }}" data-phone="{{ $cst->TLP ?? '' }}">
                            {{ $cst->DES }} {{ !empty($cst->TLP) ? '- ' . $cst->TLP : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Input Sales -->
            <div class="space-y-1">
                <label for="inputSalesDraft" class="block text-sm font-semibold text-black">
                    Sales
                </label>
                <select
                    id="inputSalesDraft"
                    class="searchable-select w-full bg-white border border-gray-800 rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                    <option value="">Cari / Pilih Sales</option>
                    @foreach($salesList ?? [] as $sls)
                        <option value="{{ $sls->DES }}" data-phone="{{ $sls->HP ?? '' }}">{{ $sls->DES }} {{ !empty($sls->HP) ? '- ' . $sls->HP : '' }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Tombol Aksi (Batal & Konfirmasi Simpan Draft) -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <button
                    type="button"
                    onclick="tutupModalTambahArsip()"
                    class="w-full bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-black rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Batal
                </button>
                <button
                    type="button"
                    onclick="simpanDraftTransaksi()"
                    class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                    Konfirmasi
                </button>
            </div>
        </div>

    </div>
</div>

{{-- 7. MODAL TUTUP KASIR --}}
<div
    id="modalTutupKasir"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

    <!-- Card Modal -->
    <div class="w-full max-w-sm bg-[#F8FAFC] rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-200">

        <!-- Judul Modal -->
        <div>
            <h3 class="text-lg font-bold text-black tracking-tight">Tutup Kasir</h3>
            <p class="text-xs text-gray-500 mt-0.5">Ringkasan transaksi dan pencocokan uang tunai fisik.</p>
        </div>

        <!-- Ringkasan Rincian Kasir -->
        <div class="space-y-2 text-sm text-black bg-white p-3.5 rounded-xl border border-gray-200">
            <!-- Saldo Awal -->
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-600">Saldo Awal (Tunai)</span>
                <span id="labelTutupSaldoAwal" class="font-semibold text-black">Rp 0</span>
            </div>

            <!-- Penjualan Tunai -->
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-600">Penjualan Tunai</span>
                <span id="labelTutupPenjualanTunai" class="font-semibold text-black">Rp 0</span>
            </div>

            <!-- Penjualan Non-Tunai -->
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-600">Penjualan Non-Tunai</span>
                <span id="labelTutupPenjualanNonTunai" class="font-semibold text-black">Rp 0</span>
            </div>

            <!-- Mutasi Keluar -->
            <div class="flex justify-between items-center text-xs">
                <span class="text-gray-600">Mutasi Keluar</span>
                <span id="labelTutupMutasiKeluar" class="font-semibold text-black">Rp 0</span>
            </div>

            <!-- Total Kas Tunai Seharusnya (Sistem) -->
            <div class="flex justify-between items-center pt-2 border-t border-gray-200 text-xs">
                <span class="font-bold text-gray-800">Kas Tunai Sistem:</span>
                <span id="labelTutupSaldoAkhir" class="font-bold text-black text-sm">Rp 0</span>
            </div>
        </div>

        <!-- Input Nominal Akhir Tunai (Fisik) -->
        <div class="space-y-1.5 pt-1">
            <label for="inputKasAkhirTunai" class="block text-xs font-bold text-black uppercase tracking-wider">
                Kas Akhir Tunai Fisik <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input
                    type="text"
                    inputmode="numeric"
                    id="inputKasAkhirTunai"
                    placeholder="Rp 0"
                    oninput="formatDanHitungSelisihTutupKasir(this)"
                    class="input-rupiah w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-black font-semibold outline-none focus:border-[#101D36] focus:ring-1 focus:ring-[#101D36] pr-8" />
                <button
                    type="button"
                    onclick="clearInputKasAkhir()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black font-bold text-xs cursor-pointer">
                    ✕
                </button>
            </div>
            <p class="text-[11px] text-gray-500">Hitung &amp; masukkan total uang fisik tunai di laci kasir.</p>
        </div>

        <!-- Box Selisih Kas -->
        <div id="boxTutupSelisih" class="p-3 rounded-xl border border-gray-200 bg-gray-50 transition-all">
            <div class="flex justify-between items-center text-xs font-semibold">
                <span class="text-gray-700">Selisih Kas Tunai:</span>
                <span id="labelTutupSelisih" class="text-sm font-bold text-gray-500">Rp 0</span>
            </div>
            <div id="descTutupSelisih" class="text-[11px] text-gray-500 mt-0.5">
                Masukkan nominal uang fisik tunai di atas untuk mengetahui selisih.
            </div>
        </div>

        <!-- Tombol Aksi (Batal & Tutup) -->
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button
                type="button"
                onclick="tutupModalTutupKasir()"
                class="w-full bg-white border border-gray-800 hover:bg-gray-100 active:bg-gray-200 text-black rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                Batal
            </button>
            <button
                type="button"
                id="btnEksekusiTutupKasir"
                onclick="prosesTutupKasir()"
                class="w-full bg-[#0F1D3A] hover:bg-[#0A1428] active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-bold transition-colors cursor-pointer">
                Tutup Kasir
            </button>
        </div>

    </div>
</div>

{{-- 8. MODAL KONFIRMASI PEMBAYARAN --}}
<div
    id="modalKonfirmasiPembayaran"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">

    <!-- Card Modal -->
    <div class="w-full max-w-sm bg-secondary-bw rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-100">

        <!-- Judul & Subtitle -->
        <div>
            <h3 class="text-xl font-bold text-black tracking-tight">Konfirmasi Pembayaran</h3>
            <p id="konfirmasiSubtitle" class="text-sm text-gray-700 mt-1 font-normal leading-relaxed">
                Selesaikan transaksi sebesar Rp 0, untuk 0 item?
            </p>
        </div>

        <!-- Form Konfirmasi -->
        <div class="space-y-3.5 pt-1">
            <!-- Field 1: Uang Diterima (HANYA TAMPIL JIKA METODE TUNAI) -->
            <div id="wrapperUangDiterima" class="space-y-1">
                <label for="inputUangDiterima" class="block text-sm font-semibold text-black">
                    Uang Diterima
                </label>
                <input
                    type="text"
                    id="inputUangDiterima"
                    oninput="formatDanHitungUangDiterima(this)"
                    placeholder="Rp 0"
                    class="w-full bg-white border rounded-xl px-4 py-2.5 text-sm text-black placeholder-gray-400 outline-none focus:border-[#101D36] focus:ring-1 focus:ring-[#101D36] font-medium" />
            </div>

            <!-- Field 2: Pelanggan -->
            <div class="space-y-1">
                <label for="inputPelangganKonfirmasi" class="block text-sm font-semibold text-black">
                    Pelanggan
                </label>
                <select
                    id="inputPelangganKonfirmasi"
                    class="searchable-select w-full bg-white border rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                    <option value="">Cari / Pilih Pelanggan</option>
                    @foreach($customers ?? [] as $cst)
                        <option value="{{ $cst->DES }}" data-phone="{{ $cst->TLP ?? '' }}">
                            {{ $cst->DES }} {{ !empty($cst->TLP) ? '- ' . $cst->TLP : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Field 3: Sales -->
            <div class="space-y-1">
                <label for="inputSalesKonfirmasi" class="block text-sm font-semibold text-black">
                    Sales
                </label>
                <select
                    id="inputSalesKonfirmasi"
                    class="searchable-select w-full bg-white border rounded-xl px-4 py-2.5 text-sm text-black outline-none font-medium">
                    <option value="">Cari / Pilih Sales</option>
                    @foreach($salesList ?? [] as $sls)
                    <option value="{{ $sls->DES }}" data-phone="{{ $sls->HP ?? '' }}">{{ $sls->DES }} {{ !empty($sls->HP) ? '- ' . $sls->HP : '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tombol Aksi (Batal & Konfirmasi) -->
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button
                type="button"
                onclick="tutupModalKonfirmasiPembayaran()"
                class="w-full bg-white border hover:bg-gray-50 active:bg-gray-100 text-black rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer">
                Batal
            </button>
            <button
                type="button"
                id="btnEksekusiBayar"
                onclick="eksekusiPembayaranFinal()"
                class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm">
                Konfirmasi
            </button>
        </div>

    </div>
</div>
