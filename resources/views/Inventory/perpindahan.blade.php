@extends('layouts.app')

@section('title', 'Perpindahan Gudang - Elektronik')
@section('header_title', 'Perpindahan Gudang')

@section('content')
<?php
    // FRONTEND ONLY dummy rows. TODO(backend): replace with RP paginator.
    $opsiSort = ['stok-banyak'=>'Stok Terbanyak','stok-sedikit'=>'Stok Tersedikit','a-z'=>'Nama (A-Z)','z-a'=>'Nama (Z-A)','new-old'=>'Terbaru','old-new'=>'Terlama'];
    $sort = request('sort'); if (!isset($opsiSort[$sort])) $sort = 'stok-banyak';
    $keyword = (string) request('keyword', '');
    $rows = [
        ['GD000000014','Pindah aja','Gudang Semarang 2','Gudang Semarang 2','Request',9],
        ['GD000000013','Pindah ke gudang baru','Gudang Bandung 2','Gudang Bandung 2','Request',8],
        ['GD000000003','Pindah deket toko baru','Gudang Bandung','Gudang Bandung','Request',7],
        ['GD000000007','acumalaka','Gudang Bekasi','Gudang Bekasi','Pending',7],
        ['GD000000015','Pindah gabut','Gudang Medan 2','Gudang Medan 2','Pending',7],
        ['GD000000010','Pindah ke gudang baru','Gudang Palembang','Gudang Palembang','Diterima',7],
        ['GD000000004','Pindah ke gudang lama','Gudang Semarang','Gudang Semarang','Diterima',7],
        ['GD000000012','Pindah ke gudang khusu laptop','Gudang Surabaya 2','Gudang Surabaya 2','Diterima',7],
        ['GD000000001','acumalaka','Gudang Utama Jakarta','Gudang Utama Jakarta','Diterima',7],
        ['GD000000009','Pindah depok','Gudang Depok','Gudang Depok','Diterima',6],
    ];
    $badge = ['Request'=>'bg-gray-100 border-gray-400 text-gray-500','Pending'=>'bg-amber-100 border-amber-400 text-amber-400','Diterima'=>'bg-green-200 border-green-600 text-green-800'];

    $btnBase   = 'h-7 w-7 border rounded-md flex items-center justify-center text-xs transition-all duration-200';
    $btnNormal = $btnBase . ' border-[#A8B1C1] text-[#142854] hover:bg-gray-100 hover:border-[#142854] active:bg-gray-200';
    $btnAktif  = $btnBase . ' font-semibold bg-[#142854] text-white border-[#142854] hover:bg-primary-sdb';
    $btnOff    = $btnBase . ' border-[#A8B1C1] text-gray-300 pointer-events-none';
?>

<div class="flex-1 flex flex-col h-full overflow-y-auto bg-secondary-bw p-4 md:p-6">
    <div class="flex items-center justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <div class="relative w-64 md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-black">
                    <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                </span>
                <input id="searchPerpindahan" type="text" value="<?= e($keyword) ?>" placeholder="Cari gudang" class="w-full bg-white border border-gray-400 rounded-lg pl-10 pr-4 py-2 text-sm text-black placeholder-gray-500 hover:border-primary-db focus:outline-none focus:border-primary-db focus:ring-1 focus:ring-primary-db">
            </div>
            <details id="sortDropdown" class="relative group">
                <summary class="list-none [&::-webkit-details-marker]:hidden h-9.5 px-3.5 bg-white border border-gray-800 rounded-xl flex items-center justify-between gap-2.5 text-sm font-medium text-black hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 cursor-pointer shadow-xs min-w-[220px]">
                    <span>Urutkan: <?= e($opsiSort[$sort]) ?></span>
                    <svg class="w-4 h-4 text-gray-600 group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </summary>
                <div class="absolute left-0 mt-2 w-52 bg-secondary-bw rounded-2xl p-2 shadow-2xl border border-gray-300 z-50 space-y-1.5">
                    <?php foreach ($opsiSort as $k=>$v): ?>
                    <button type="button" class="w-full bg-white border text-black rounded-xl px-3.5 py-2.5 text-left text-sm flex items-center justify-between hover:bg-primary-db/5 hover:border-primary-db active:bg-primary-db/10 transition-all duration-200 shadow-xs cursor-pointer"><?= e($v) ?><?php if($sort===$k): ?><span>✓</span><?php endif; ?></button>
                    <?php endforeach; ?>
                </div>
            </details>
        </div>
        <button type="button" onclick="bukaModal('pindahBarangModal')" class="h-9.5 px-4 bg-primary-db hover:bg-primary-sdb active:bg-dark-navy text-white rounded-lg flex items-center gap-1.5 text-sm font-semibold transition-all duration-200 cursor-pointer shadow-xs hover:shadow"><span class="text-lg leading-none font-bold">+</span><span>Pindah Barang</span></button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-300 flex flex-col">
        <div class="overflow-visible">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-primary-db text-white text-xs font-semibold">
                        <th class="py-2.5 px-4 rounded-tl-lg">Kode Perpindahan</th>
                        <th class="py-2.5 px-4">Nama Perpindahan</th>
                        <th class="py-2.5 px-4">Gudang Asal</th>
                        <th class="py-2.5 px-4">Gudang Tujuan</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                        <th class="py-2.5 px-4 text-center">Jumlah Barang Dipindahkan</th>
                        <th class="py-2.5 px-4 text-center w-20 rounded-tr-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody id="perpindahanBody" class="divide-y divide-gray-200 text-sm text-black">
                    <?php foreach ($rows as $r): ?>
                    <?php [$kode,$nama,$asal,$tujuan,$st,$jml] = $r; ?>
                    <tr class="perpindahan-row hover:bg-gray-50/80" data-search="<?= e(strtolower($kode.' '.$nama.' '.$asal.' '.$tujuan)) ?>">
                        <td class="py-3.5 px-4 font-medium"><?= e($kode) ?></td>
                        <td class="py-3.5 px-4"><?= e($nama) ?></td>
                        <td class="py-3.5 px-4"><?= e($asal) ?></td>
                        <td class="py-3.5 px-4"><?= e($tujuan) ?></td>
                        <td class="py-3.5 px-4 text-center">
                            <span class="inline-block min-w-[130px] px-4 py-1 text-sm font-medium rounded border <?= $badge[$st] ?>"><?= e($st) ?></span>
                        </td>
                        <td class="py-3.5 px-4 text-center"><?= (int)$jml ?></td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="relative inline-block">
                                <button type="button" onclick="toggleActionMenu(this)" class="btn-aksi h-8 w-8 border border-gray-400 bg-white hover:bg-gray-300 hover:border-gray-900 hover:shadow-xs active:bg-gray-200 rounded-md inline-flex items-center justify-center transition-all duration-200 shadow-2xs cursor-pointer" title="Aksi Perpindahan">
                                    <svg class="w-4 h-4 text-primary-db" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                </button>
                                <div class="action-menu absolute right-0 top-full mt-1 w-28 bg-white border border-gray-300 rounded-lg shadow-xl py-1 z-50 text-left hidden">
                                    <button type="button" onclick="bukaModal('detailModal');tutupSemuaMenu();" class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-primary-db/5 hover:text-primary-db transition-all duration-200 flex items-center gap-2 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        <span>Detail</span>
                                    </button>
                                    <button type="button" onclick="bukaModal('pindahBarangModal');tutupSemuaMenu();" class="w-full px-3 py-1.5 text-xs font-semibold text-gray-800 hover:bg-primary-db/5 hover:text-primary-db transition-all duration-200 flex items-center gap-2 cursor-pointer">
                                        <svg class="w-3.5 h-3.5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" /></svg>
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" onclick="return confirm('Hapus perpindahan ini? (UI only)')" class="w-full px-3 py-1.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors flex items-center gap-2 cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4v3M4 7h16" /></svg>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="px-4 py-3 bg-white border-t border-gray-200 flex items-center justify-between text-xs text-[#142854] rounded-b-lg">
            <div>Menampilkan 1-10 dari 15 item</div>
            <div class="flex items-center gap-1">
                <span class="<?= $btnOff ?>">‹</span>
                <span class="<?= $btnAktif ?>">1</span>
                <span class="<?= $btnNormal ?>">2</span>
                <span class="<?= $btnNormal ?>">›</span>
            </div>
        </div>
    </div>
</div>

<!-- MODALS frontend skeleton, TODO backend wire -->
<div id="pindahBarangModal" class="hidden fixed inset-0 z-[100] items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-db">Pindah Barang</h2>
            <button type="button" onclick="tutupModal('pindahBarangModal')" class="text-xl cursor-pointer">×</button>
        </div>
        <div class="p-6 space-y-4 overflow-y-auto">
            <div><label class="block text-sm font-semibold mb-1.5">Nama Perpindahan</label>
            <input type="text" placeholder="Contoh: Pindah ke gudang baru" class="w-full border border-gray-400 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-primary-db"></div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="block text-sm font-semibold mb-1.5">Gudang Asal</label>
                <select class="w-full border border-gray-400 rounded-xl px-3.5 py-2.5 text-sm"><option>Pilih gudang asal</option><option>Gudang Utama Jakarta</option><option>Gudang Bandung</option></select></div>
                <div><label class="block text-sm font-semibold mb-1.5">Gudang Tujuan</label>
                <select class="w-full border border-gray-400 rounded-xl px-3.5 py-2.5 text-sm"><option>Pilih gudang tujuan</option><option>Gudang Bandung 2</option><option>Gudang Depok</option></select></div>
            </div>
            <div><label class="block text-sm font-semibold mb-1.5">Scan / Cari Barang (BC_OUT)</label>
            <input type="text" placeholder="Scan barcode atau ketik nama barang..." class="w-full border border-gray-400 rounded-xl px-3.5 py-2.5 text-sm focus:outline-none focus:border-primary-db">
            <p class="text-xs text-gray-500 mt-1">TODO backend: autocomplete itmdt belum terjual per gudang asal.</p></div>
        </div>
        <div class="flex gap-3 px-6 py-4 border-t border-gray-200">
            <button type="button" onclick="tutupModal('pindahBarangModal')" class="flex-1 py-2.5 border border-black/80 rounded-xl text-sm font-semibold cursor-pointer">Batal</button>
            <button type="button" onclick="tutupModal('pindahBarangModal')" class="flex-1 py-2.5 bg-primary-db text-white rounded-xl text-sm font-semibold cursor-pointer">Simpan (UI only)</button>
        </div>
    </div>
</div>
<div id="detailModal" class="hidden fixed inset-0 z-[100] items-center justify-center bg-black/50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-db">Detail Perpindahan</h2>
            <button type="button" onclick="tutupModal('detailModal')" class="text-xl cursor-pointer">×</button>
        </div>
        <div class="p-6 text-sm space-y-2">
            <p><b>Kode:</b> GD000000014</p><p><b>Nama:</b> Pindah aja</p>
            <p><b>Status:</b> Request</p><p><b>Jumlah:</b> 9 barang</p>
            <p class="text-xs text-gray-500">TODO backend: render rp + rpd lines.</p>
        </div>
        <div class="px-6 py-4 border-t flex justify-end">
            <button type="button" onclick="tutupModal('detailModal')" class="py-2.5 px-6 bg-primary-db text-white rounded-xl text-sm font-semibold cursor-pointer">Tutup</button>
        </div>
    </div>
</div>
<script>
function toggleActionMenu(b){const m=b.parentElement.querySelector('.action-menu');document.querySelectorAll('.action-menu').forEach(i=>{if(i!==m)i.classList.add('hidden')});m.classList.toggle('hidden');}
function tutupSemuaMenu(){document.querySelectorAll('.action-menu').forEach(m=>m.classList.add('hidden'));}
function bukaModal(id){const m=document.getElementById(id);m.classList.remove('hidden');m.classList.add('flex');}
function tutupModal(id){const m=document.getElementById(id);m.classList.remove('flex');m.classList.add('hidden');}
document.addEventListener('click',e=>{if(!e.target.closest('.action-menu')&&!e.target.closest('.btn-aksi'))document.querySelectorAll('.action-menu').forEach(m=>m.classList.add('hidden'));const s=document.getElementById('sortDropdown');if(s&&!e.target.closest('#sortDropdown'))s.removeAttribute('open');});
document.getElementById('searchPerpindahan')?.addEventListener('input',function(){const q=this.value.toLowerCase().trim();document.querySelectorAll('.perpindahan-row').forEach(r=>{r.style.display=!q||(r.dataset.search||'').includes(q)?'':'none';});});
</script>
@endsection

