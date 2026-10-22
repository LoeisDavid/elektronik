<!-- Modal Struk Pembayaran -->
<div 
    id="modalStrukPembayaran" 
    onclick="tutupModalStrukKlikLuar(event)"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-3 sm:p-4 overflow-y-auto">
    
    <!-- Card Modal -->
    <div 
        onclick="event.stopPropagation()"
        class="w-full max-w-sm bg-white rounded-3xl p-5 sm:p-6 shadow-2xl border border-gray-100 animate-fade-in flex flex-col max-h-[92vh] my-auto">

        <!-- Judul Modal -->
        <div class="flex items-center justify-between pb-1 shrink-0">
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

        <!-- Wrapper Scrollable jika Struk Kepanjangan -->
        <div class="overflow-y-auto flex-1 pr-1 -mr-1 my-2 overscroll-contain rounded-2xl">
            <!-- Tampilan Kertas Struk Sesuai Mockup -->
            <div id="areaStrukPrint" class="bg-white p-5 rounded-2xl border border-dashed border-gray-300 text-xs font-sans text-gray-800 space-y-3">
                
                <!-- Header Toko / Brand Name -->
                <div class="text-center space-y-1">
                    <h2 class="text-2xl font-serif font-extrabold text-black tracking-tight" id="strukNamaToko">Pomelo.</h2>
                    <p class="text-[11px] text-gray-400 font-medium mt-1">No. Transaksi</p>
                    <p id="strukNoNota" class="text-sm font-bold text-gray-900 font-mono tracking-wider">-</p>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Detail Transaksi (Kiri) -->
                <div class="space-y-0.5 text-xs text-gray-500 font-medium">
                    <p id="strukTanggal">-</p>
                    <p id="strukWaktu">-</p>
                    <p>Dilayani oleh: <span id="strukKasir" class="text-gray-800 font-semibold">Kasir</span></p>
                    
                    {{-- Info Pelanggan, Desa & Alamat --}}
                    <div id="wrapperStrukPelanggan" class="hidden pt-1 mt-1 border-t border-dashed border-gray-200 space-y-0.5 text-xs text-gray-500 font-medium">
                        <p id="rowStrukCustomerNama" class="hidden">Pelanggan: <span id="strukCustomerNama" class="text-gray-800 font-semibold">-</span></p>
                        <p id="rowStrukCustomerDesa" class="hidden">Desa: <span id="strukCustomerDesa" class="text-gray-800 font-semibold">-</span></p>
                        <p id="rowStrukCustomerAlamat" class="hidden">Alamat: <span id="strukCustomerAlamat" class="text-gray-800 font-semibold">-</span></p>
                    </div>
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Item / Produk -->
                <div id="strukListProduk" class="space-y-2.5">
                    <!-- Diisi secara dinamis via JavaScript -->
                </div>

                <div class="border-b border-dashed border-gray-300 my-2"></div>

                <!-- Rincian Subtotal, Diskon & Total -->
                <div class="space-y-1.5 pt-0.5 text-xs">
                    <!-- Subtotal -->
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span id="strukSubtotal" class="font-semibold text-gray-900">Rp 0</span>
                    </div>

                    <!-- Diskon Per Item (Tampil jika ada) -->
                    <div id="wrapperStrukDiskonItem" class="hidden justify-between text-gray-600">
                        <span>Diskon Per Item</span>
                        <span id="strukDiskonItem" class="font-semibold text-red-600">- Rp 0</span>
                    </div>

                    <!-- Diskon Global (Tampil jika ada) -->
                    <div id="wrapperStrukDiskonGlobal" class="hidden justify-between text-gray-600">
                        <span id="labelStrukDiskonGlobal">Diskon Global</span>
                        <span id="strukDiskonGlobal" class="font-semibold text-red-600">- Rp 0</span>
                    </div>

                    <!-- Voucher Global (Tampil jika ada) -->
                    <div id="wrapperStrukVoucher" class="hidden justify-between text-gray-600">
                        <span>Voucher</span>
                        <span id="strukVoucher" class="font-semibold text-red-600">- Rp 0</span>
                    </div>

                    <!-- PPN (Tampil jika ada) -->
                    <div id="wrapperStrukPPN" class="hidden justify-between text-gray-600">
                        <span id="labelStrukPPN">PPN</span>
                        <span id="strukPPN" class="font-semibold text-gray-900">+ Rp 0</span>
                    </div>

                    <!-- Total -->
                    <div class="flex justify-between text-sm font-bold text-gray-900 pt-1 border-t border-gray-100">
                        <span>Total</span>
                        <span id="strukTotal" class="text-base font-extrabold text-black">Rp 0</span>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="flex justify-between text-gray-600 pt-0.5">
                        <span>Pembayaran</span>
                        <span id="strukMetode" class="font-semibold text-gray-900">Tunai</span>
                    </div>

                    <!-- Rincian Tunai (Dibayar & Kembali) -->
                    <div id="wrapperDetailTunai" class="hidden space-y-1 pt-0.5 text-gray-600">
                        <div class="flex justify-between">
                            <span>Dibayar</span>
                            <span id="strukDibayar" class="font-semibold text-gray-900">Rp 0</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Kembali</span>
                            <span id="strukKembali" class="font-semibold text-gray-900">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>
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