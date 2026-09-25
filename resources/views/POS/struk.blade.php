<!-- Modal Struk Pembayaran -->
<div 
    id="modalStrukPembayaran" 
    onclick="tutupModalStrukKlikLuar(event)"
    class="fixed inset-0 bg-black/40 backdrop-blur-[2px] hidden items-center justify-center z-50 p-4">
    
    <!-- Card Modal -->
    <div 
        onclick="event.stopPropagation()"
        class="w-full max-w-sm bg-white rounded-2xl p-6 shadow-2xl space-y-4 border border-gray-100 animate-fade-in">

        <!-- Tampilan Struk (Area yang akan di-print/download) -->
        <div id="areaStrukPrint" class="bg-white p-4 rounded-xl border border-dashed border-gray-300 text-xs font-sans text-gray-800 space-y-3">
            
            <!-- Header Toko -->
            <div class="text-center space-y-1">
                <h2 class="text-xl font-extrabold text-black tracking-wider uppercase" id="strukNamaToko">NamaToko</h2>
                <p class="text-[11px] text-gray-500 leading-tight" id="strukAlamatToko">
                    Medokan Ayu, Kec. Rungkut, Surabaya<br>
                    No Telp: 0881-0268-77983
                </p>
            </div>

            <div class="border-b border-dashed border-gray-300 my-2"></div>

            <!-- Detail Transaksi -->
            <div class="space-y-1 text-gray-600">
                <div class="flex justify-between">
                    <span>No. Transaksi</span>
                    <span id="strukNoNota" class="font-semibold text-gray-900">2026-0001</span>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal</span>
                    <span id="strukTanggal">13/09/2026 (23:01)</span>
                </div>
                <div class="flex justify-between">
                    <span>Dilayani oleh</span>
                    <span id="strukKasir">User 2</span>
                </div>
            </div>

            <div class="border-b border-dashed border-gray-300 my-2"></div>

            <!-- Item / Produk -->
            <div id="strukListProduk" class="space-y-2">
                <!-- Diisi secara dinamis via JavaScript -->
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold text-gray-900">1x NamaProduk</p>
                    </div>
                    <span class="font-semibold text-gray-900">Rp 20.000.000</span>
                </div>
            </div>

            <div class="border-b border-dashed border-gray-300 my-2"></div>

            <!-- Total & Pembayaran -->
            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between text-sm font-bold text-gray-900">
                    <span>Total</span>
                    <span id="strukTotal" class="text-base">Rp 0</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Metode pembayaran</span>
                    <span id="strukMetode" class="font-semibold text-gray-900">QRIS</span>
                </div>
                <div id="wrapperDetailTunai" class="hidden space-y-1 pt-1 border-t border-gray-100 text-gray-600">
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

        <!-- Tombol Aksi (Print & Download / Tutup) -->
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button 
                type="button" 
                onclick="" 
                class="w-full bg-white border active:bg-black text-black rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15V3m0 12l-4-4m4 4l4-4M2 17l.621 2.485A2 2 0 0 0 4.561 21h14.877a2 2 0 0 0 1.94-1.515L22 17" />
                </svg>
                Download
            </button>
            <button 
                type="button" 
                onclick="cetakStruk()" 
                class="w-full bg-primary-db hover:bg-primary-sdb active:bg-black text-white rounded-xl py-2.5 text-center text-sm font-semibold transition-colors cursor-pointer shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Struk
            </button>
        </div>

    </div>
</div>