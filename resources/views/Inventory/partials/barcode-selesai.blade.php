{{-- resources/views/inventory/partials/stok-barcode.blade.php
     Di-include SEKALI di inventory/item.blade.php (sebelum @endsection):
         @include('inventory.partials.stok-barcode')
     Syarat: tombol "Next" pada modal tambah stok memakai id="btnNextStok" --}}
@php
    $namaItmMap = collect($daftarITM ?? [])->pluck('des', 'itm');
    $stokBatch  = collect(session('stok_batch', []))->map(fn ($b) => [
        'bc'  => $b['bc'],
        'itm' => $b['itm'],
        'des' => $namaItmMap[$b['itm']] ?? $b['itm'],
    ])->values();
@endphp

{{-- MODAL HASIL BARCODE (muncul setelah klik Next) --}}
<div id="stokBarcodeModal" class="fixed inset-0 z-[70] hidden items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] flex flex-col shadow-lg">
        <div class="px-6 pt-6 pb-3">
            <h3 class="text-base font-bold text-black">Stok berhasil dibuat</h3>
            <p id="stokBarcodeInfo" class="text-xs text-gray-700"></p>
        </div>
        <div id="stokBarcodeList" class="px-6 pb-4 overflow-y-auto grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-2">
            <button type="button" id="stokBarcodeKembali"
                    class="py-1.5 px-4 bg-white border border-gray-400 text-black rounded-xl text-xs font-semibold">Kembali</button>
            <button type="button" id="stokBarcodePrintAll"
                    class="py-1.5 px-4 bg-white border border-gray-400 text-black rounded-xl text-xs font-semibold">Print Semua</button>
            <button type="button" id="stokBarcodeSelesai"
                    class="py-1.5 px-4 bg-primary-db text-white rounded-xl text-xs font-semibold">Selesai</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
<script>
(function () {
    var BATCH  = @json($stokBatch);
    var FORMAT = 'CODE128';

    var btnNext = document.getElementById('btnNextStok');
    var modal   = document.getElementById('stokBarcodeModal');
    var list    = document.getElementById('stokBarcodeList');
    var info    = document.getElementById('stokBarcodeInfo');
    var btnAll  = document.getElementById('stokBarcodePrintAll');

    function render() {
        list.innerHTML = '';
        if (!BATCH.length) {
            info.textContent = 'Belum ada stok yang berhasil disimpan.';
            btnAll.classList.add('hidden');
            return;
        }
        btnAll.classList.remove('hidden');
        info.textContent = BATCH.length + ' barcode berhasil dibuat.';

        BATCH.forEach(function (b) {
            var card = document.createElement('div');
            card.className = 'border border-gray-300 rounded-xl p-3 text-center';
            var nm = document.createElement('div');
            nm.className = 'text-xs text-gray-700 mb-1';
            nm.textContent = b.des;
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            card.appendChild(nm);
            card.appendChild(svg);
            list.appendChild(card);
            JsBarcode(svg, b.bc, { format: FORMAT, width: 1.6, height: 60, displayValue: true, fontSize: 13, margin: 4 });
        });
    }

    function buka()  { render(); modal.classList.remove('hidden'); modal.classList.add('flex'); }
    function tutup() { modal.classList.add('hidden'); modal.classList.remove('flex'); }

    if (btnNext) btnNext.addEventListener('click', buka);
    document.getElementById('stokBarcodeKembali').addEventListener('click', tutup);

    // Print semua barcode SAJA (iframe tersembunyi)
    btnAll.addEventListener('click', function () {
        var html = '';
        list.querySelectorAll('div.border').forEach(function (card) {
            html += '<div class="lb"><div class="nm">' + card.firstChild.textContent + '</div>' +
                    card.querySelector('svg').outerHTML + '</div>';
        });

        var frame = document.createElement('iframe');
        frame.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
        document.body.appendChild(frame);
        var doc = frame.contentWindow.document;
        doc.open();
        doc.write(
            '<html><head><title>Barcode Stok</title><style>' +
            '@page{margin:8mm}body{margin:0;font-family:Arial,sans-serif}' +
            '.wrap{display:flex;flex-wrap:wrap;gap:8mm}' +
            '.lb{width:60mm;text-align:center;break-inside:avoid;page-break-inside:avoid}' +
            '.nm{font-size:11px;margin-bottom:2px}svg{max-width:100%}' +
            '</style></head><body><div class="wrap">' + html + '</div></body></html>'
        );
        doc.close();
        frame.contentWindow.focus();
        setTimeout(function () {
            frame.contentWindow.print();
            setTimeout(function () { document.body.removeChild(frame); }, 1000);
        }, 300);
    });

    // Selesai: kosongkan daftar di session, lalu muat ulang halaman Inventory
    document.getElementById('stokBarcodeSelesai').addEventListener('click', function () {
        fetch(@json(route('inventory.item.stok.selesai')), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' }
        }).finally(function () {
            window.location.href = @json(route('inventory.item'));
        });
    });
})();
</script>