{{-- ========================================================= --}}
{{-- MODAL STRUK (SESUAI DESAIN KASIR / POMELO)                --}}
{{-- ========================================================= --}}
@include('POS.struk')

{{-- ========================================================= --}}
{{-- FORM PRINT STRUK TERSEMBUNYI                              --}}
{{-- ========================================================= --}}
<form
    id="formPrintStruk"
    method="POST"
    action="{{ route('POS.struk.print') }}"
    target="_blank"
    class="hidden"
>
    @csrf
    <input type="hidden" name="ref_no"          id="printRefNo">
    <input type="hidden" name="tanggal"         id="printTanggal">
    <input type="hidden" name="waktu"           id="printWaktu">
    <input type="hidden" name="kasir"           id="printKasir">
    <input type="hidden" name="metode"          id="printMetode">
    <input type="hidden" name="subtotal"        id="printSubtotal">
    <input type="hidden" name="discount_item"   id="printDiscountItem">
    <input type="hidden" name="discount_global" id="printDiscountGlobal">
    <input type="hidden" name="voucher"         id="printVoucher">
    <input type="hidden" name="ppn"             id="printPpn">
    <input type="hidden" name="ppn_nominal"     id="printPpnNominal">
    <input type="hidden" name="total"           id="printTotal">
    <input type="hidden" name="dibayar"         id="printDibayar">
    <input type="hidden" name="kembalian"       id="printKembalian">
    <input type="hidden" name="customer_nama"   id="printCustomerNama">
    <input type="hidden" name="customer_desa"   id="printCustomerDesa">
    <input type="hidden" name="customer_alamat" id="printCustomerAlamat">
    <input type="hidden" name="items"           id="printItems">
</form>

{{-- ========================================================= --}}
{{-- MODAL PRINT LAPORAN PENJUALAN (RANGE TANGGAL)             --}}
{{-- ========================================================= --}}
<div
    id="modalPrintLaporan"
    class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 transition-opacity"
>
    <div class="bg-white rounded-2xl border border-gray-300 w-full max-w-md p-6 shadow-2xl space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-primary-db/10 text-primary-db flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900 leading-tight">Cetak Laporan Penjualan</h3>
                    <p class="text-xs text-gray-500">Pilih rentang tanggal transaksi untuk dicetak</p>
                </div>
            </div>
            <button
                type="button"
                onclick="closeModalPrintLaporan()"
                class="text-gray-400 hover:text-gray-600 rounded-lg p-1.5 transition-colors cursor-pointer"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form action="{{ route('POS.transaksi.cetak') }}" method="GET" target="_blank" onsubmit="closeModalPrintLaporan()">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Dari Tanggal
                    </label>
                    <input
                        type="date"
                        name="tanggal_dari"
                        value="{{ request('tanggal_dari', now()->subDays(30)->toDateString()) }}"
                        class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db shadow-xs"
                    >
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Sampai Tanggal
                    </label>
                    <input
                        type="date"
                        name="tanggal_sampai"
                        value="{{ request('tanggal_sampai', now()->toDateString()) }}"
                        class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm text-gray-900 focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db shadow-xs"
                    >
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-5 mt-5 border-t border-gray-200">
                <button
                    type="button"
                    onclick="closeModalPrintLaporan()"
                    class="px-4 py-2 border border-gray-300 bg-white hover:bg-gray-100 text-gray-700 rounded-xl text-xs font-semibold transition-colors cursor-pointer"
                >
                    Batal
                </button>
                <button
                    type="submit"
                    class="px-5 py-2 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-xl text-xs font-bold transition-all shadow-xs cursor-pointer flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.673-2.684-1.28-4.329m13.12 4.329c.24-1.076.673-2.684 1.28-4.329m-14.4 0a17.962 17.962 0 0114.4 0m-14.4 0a17.962 17.962 0 0014.4 0M6 18h12a2 2 0 002-2v-4a2 2 0 00-2-2H6a2 2 0 00-2 2v4a2 2 0 002 2zM6 14h12M9 10V6a2 2 0 012-2h2a2 2 0 012 2v4" />
                    </svg>
                    <span>Cetak Laporan</span>
                </button>
            </div>
        </form>
    </div>
</div>
