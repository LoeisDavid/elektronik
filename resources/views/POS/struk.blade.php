@include('POS.partials.nota-styles')

<!-- Modal Struk Pembayaran -->
<div
    id="modalStrukPembayaran"
    onclick="tutupModalStrukKlikLuar(event)"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-3 sm:p-4 overflow-y-auto">

    <!-- Card Modal -->
    <div
        onclick="event.stopPropagation()"
        class="w-full max-w-[920px] bg-white rounded-3xl p-4 sm:p-6 shadow-2xl border border-gray-100 animate-fade-in flex flex-col max-h-[92vh] my-auto">

        <!-- Judul Modal -->
        <div class="flex items-center justify-between pb-2 shrink-0">
            <h3 class="text-xl font-extrabold text-gray-900 tracking-tight">Struk</h3>
            <button
                type="button"
                onclick="tutupModalStruk()"
                class="text-gray-400 hover:text-gray-600 rounded-lg p-1 transition-colors cursor-pointer"
                title="Tutup">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Wrapper Scrollable untuk Preview Lembar Nota 215mm x 110mm -->
        <div class="overflow-auto flex-1 my-2 overscroll-contain rounded-2xl bg-[#eceff2] p-3 sm:p-4 border border-gray-200">
            <div
                id="notaPreviewRoot"
                class="nota-root"
                data-shop-name="{{ config('perusahaan.nama_perusahaan', 'Toko Bogor') }}"
                data-shop-address="{{ trim(collect([config('perusahaan.alamat'), config('perusahaan.kota')])->filter()->implode(' ')) }}"
                data-shop-phone="{{ config('perusahaan.no_telp', '') }}"
                data-shop-npwp="{{ config('perusahaan.npwp', '') }}"
            ></div>
        </div>

        <!-- Tombol Aksi (Tutup & Print Struk) -->
        <div class="grid grid-cols-2 gap-3 pt-2 shrink-0 border-t border-gray-100">
            <button
                type="button"
                onclick="tutupModalStruk()"
                class="w-full bg-white border border-gray-400 hover:bg-gray-100 active:bg-gray-200 text-gray-900 rounded-xl py-2.5 text-center text-xs font-bold transition-colors cursor-pointer shadow-xs">
                Tutup
            </button>
            <button
                type="button"
                onclick="if(typeof cetakStrukPdf === 'function'){ cetakStrukPdf(); } else if(typeof cetakStruk === 'function'){ cetakStruk(); }"
                class="w-full bg-[#1A1F36] hover:bg-black active:bg-black text-white rounded-xl py-2.5 text-center text-xs font-bold transition-colors cursor-pointer shadow-xs flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Print Struk
            </button>
        </div>

    </div>
</div>
