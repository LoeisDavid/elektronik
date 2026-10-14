<?php
namespace App\Http\Controllers;

use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\KTG;
use App\Models\MRK;
use App\Models\SPP;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ITMController extends Controller
{
    private const PER_PAGE      = 10;
    private const BATAS_MENIPIS = 5; // STOK 1..5 dianggap menipis
    const OPSI_SORT = [
    'nama_asc'        => 'Nama (A-Z)',
    'nama_desc'       => 'Nama (Z-A)',
    'tgl_terbaru'     => 'Terbaru',
    'tgl_terlama'     => 'Terlama',
    'stok_terbanyak'  => 'Stok Terbanyak',
    'stok_tersedikit' => 'Stok Tersedikit',
];

public function index(Request $request)
{
    try {
        $keyword = trim((string) $request->query('keyword', ''));
        $sort    = (string) $request->query('sort', 'nama_asc');
        if (!isset(self::OPSI_SORT[$sort])) {
            $sort = 'nama_asc';
        }
        $page = max(1, (int) $request->query('page', 1));

        // ------------------------------------------------------------
        // 1. Tabel kecil (KTG, MRK, SPP, GD) diambil SEKALI saja.
        // ------------------------------------------------------------
        $KTG = KTG::all();
        $MRK = MRK::all();
        $SPP = SPP::all();
        $GD  = GD::all();

        $namaKTG = $KTG->pluck('DES', 'KTG')->all();
        $namaMRK = $MRK->pluck('DES', 'MRK')->all();
        $namaSPP = $SPP->pluck('DES', 'SPP')->all();

        // ------------------------------------------------------------
        // 2. Ambil semua ITM (termasuk created_at untuk sort tanggal)
        // ------------------------------------------------------------
        $semuaRow = [];

        foreach (ITM::query()->toBase()->get(['ITM', 'DES', 'KTG', 'MRK', 'SPP', 'HRG', 'STOK', 'created_at']) as $itm) {
            $semuaRow[] = (object) [
                'itm'        => $itm->ITM,
                'des'        => $itm->DES,
                'ktg'        => $itm->KTG,
                'ktg_nama'   => $namaKTG[$itm->KTG] ?? '-',
                'mrk'        => $itm->MRK,
                'mrk_nama'   => $namaMRK[$itm->MRK] ?? '-',
                'spp'        => $itm->SPP,
                'spp_nama'   => $namaSPP[$itm->SPP] ?? '-',
                'hrg'        => $itm->HRG,
                'stok'       => (int) $itm->STOK,
                'created_at' => $itm->created_at ? (string) $itm->created_at : null,
            ];
        }

        // Daftar ITM untuk dropdown "Nama Barang" di modal tambah stok
        $daftarITM = $semuaRow;
        usort($daftarITM, fn ($a, $b) => strcasecmp($a->des, $b->des));

        // ------------------------------------------------------------
        // 3. Statistik (semua ITM)
        // ------------------------------------------------------------
        $stat = ['total' => count($semuaRow), 'menipis' => 0, 'habis' => 0];
        foreach ($semuaRow as $r) {
            if ($r->stok === 0) {
                $stat['habis']++;
            } elseif ($r->stok <= self::BATAS_MENIPIS) {
                $stat['menipis']++;
            }
        }

        // ------------------------------------------------------------
        // 4. Search PHP murni
        // ------------------------------------------------------------
        $hasil = $semuaRow;

        if ($keyword !== '') {
            $hasil = array_values(array_filter($semuaRow, function ($r) use ($keyword) {
                foreach ([$r->itm, $r->des, $r->ktg_nama, $r->mrk_nama, $r->spp_nama] as $field) {
                    if (mb_stripos((string) $field, $keyword) !== false) {
                        return true;
                    }
                }

                return false;
            }));
        }

        // ------------------------------------------------------------
        // 5. Sort PHP murni
        // ------------------------------------------------------------
        usort($hasil, function ($a, $b) use ($sort) {
            $nama = strcasecmp($a->des, $b->des);

            switch ($sort) {
                case 'nama_desc':
                    return -$nama;

                case 'tgl_terbaru':
                case 'tgl_terlama':
                    if ($a->created_at === $b->created_at) return $nama;
                    if ($a->created_at === null) return 1;   // tanpa tanggal selalu di bawah
                    if ($b->created_at === null) return -1;
                    $cmp = strcmp($a->created_at, $b->created_at);
                    return $sort === 'tgl_terbaru' ? -$cmp : $cmp;

                case 'stok_terbanyak':
                    return ($b->stok <=> $a->stok) ?: $nama;

                case 'stok_tersedikit':
                    return ($a->stok <=> $b->stok) ?: $nama;

                default:
                    return $nama;
            }
        });

        // ------------------------------------------------------------
        // 6. Pagination PHP murni
        // ------------------------------------------------------------
        $totalData  = count($hasil);
        $totalPages = max(1, (int) ceil($totalData / self::PER_PAGE));
        $page       = min($page, $totalPages);
        $offset     = ($page - 1) * self::PER_PAGE;

        $produk = array_slice($hasil, $offset, self::PER_PAGE);

        $pagination = [
            'page'        => $page,
            'total_data'  => $totalData,
            'total_pages' => $totalPages,
            'dari'        => $totalData > 0 ? $offset + 1 : 0,
            'sampai'      => $offset + count($produk),
        ];

        return view('inventory.item', [
            'produk'     => $produk,
            'stat'       => $stat,
            'pagination' => $pagination,
            'daftarITM'  => $daftarITM,
            'opsiSort'   => self::OPSI_SORT,
            'keyword'    => $keyword,
            'sort'       => $sort,
            'MRK'        => $MRK,
            'KTG'        => $KTG,
            'SPP'        => $SPP,
            'GD'         => $GD,
            'stokList'   => session('stok_list', []),
        ]);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan daftar barang');
    }
}

    // ================================================================
    // TAMBAH BARANG (ITM) — STOK awal selalu 0, stok masuk lewat ITMDT
    // ================================================================
    public function store(Request $request)
    {
        if ($request->has('HRG')) {
            $cleanHrg = preg_replace('/[^\d]/', '', (string) $request->input('HRG'));
            $request->merge(['HRG' => $cleanHrg !== '' ? (float) $cleanHrg : null]);
        }

        $data = $request->validate([
            'DES' => 'required|string|max:150',
            'MRK' => 'required|string|max:10',
            'KTG' => 'required|string|max:10',
            'SPP' => 'required|string|max:10',
            'HRG' => 'required|numeric|min:0',
        ], [
            'DES.required' => 'Nama barang wajib diisi.',
            'DES.string'   => 'Nama barang harus berupa teks.',
            'DES.max'      => 'Nama barang maksimal 150 karakter.',
            'MRK.required' => 'Merk wajib dipilih.',
            'KTG.required' => 'Kategori wajib dipilih.',
            'SPP.required' => 'Supplier wajib dipilih.',
            'HRG.required' => 'Harga jual wajib diisi.',
            'HRG.numeric'  => 'Harga jual harus berupa angka.',
            'HRG.min'      => 'Harga jual minimal 0.',
        ]);

        if (ITM::where('DES', $data['DES'])
                ->where('MRK', $data['MRK'])
                ->where('KTG', $data['KTG'])
                ->exists()) {
            throw ValidationException::withMessages([
                'DES' => 'Barang dengan nama, merk, dan kategori tersebut sudah ada.',
            ]);
        }

        try {
            $data['ITM'] = $this->generateCode($data['DES'], 'ITM');

            if (ITM::where('ITM', $data['ITM'])->exists()) {
                throw ValidationException::withMessages(['ITM' => 'Kode ITM sudah digunakan.']);
            }

            ITM::create($data + ['STOK' => 0]);

            return redirect()->route('inventory.item')->with('success', 'Barang berhasil ditambahkan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan barang');
        }
    }

    public function show(Request $request, string $itm)
    {
        try {
            // Data item (ITM)
            $item = Itm::findOrFail($itm);

            // Semua detail stok milik item ini (ITMDT) + hitung field turunan
            $all = Itmdt::where('ITM', $item->ITM)->get()->map(function ($d) {
                $masuk  = $d->TM ? Carbon::parse($d->TM) : null;
                $sts    = $d->sts();
                $keluar = $sts && $sts->created_at ? Carbon::parse($sts->created_at) : null;
                $gudang = $d->GD();

                return (object) [
                    'kode_stok'   => $d->BC_OUT,
                    'tgl_masuk'   => $masuk,
                    'tgl_keluar'  => $keluar,
                    'ts_masuk'    => $masuk?->timestamp ?? 0,
                    'ts_keluar'   => $keluar?->timestamp,
                    'nama_gudang' => $gudang->DES ?? '-',
                    'terjual'     => $keluar !== null,
                    'status'      => $keluar === null ? 'IN STOK' : 'Terjual',
                ];
            });

            // Ringkasan (dihitung sebelum difilter)
            $summary = [
                'total'    => $all->count(),
                'in_stok'  => $all->where('terjual', false)->count(),
                'terjual'  => $all->where('terjual', true)->count(),
            ];

            // ---------- FILTER ----------
            $q       = trim((string) $request->query('q', ''));
            $tanggal = $request->query('tanggal');
            $status  = $request->query('status', 'semua');
            $sort    = $request->query('sort', 'default');

            $rows = $all
                ->when($q !== '', function ($c) use ($q) {
                    return $c->filter(fn ($r) =>
                        stripos($r->nama_gudang, $q) !== false ||
                        stripos((string) $r->kode_stok, $q) !== false
                    );
                })
                ->when($tanggal, function ($c) use ($tanggal) {
                    return $c->filter(fn ($r) =>
                        $r->tgl_masuk?->toDateString() === $tanggal ||
                        $r->tgl_keluar?->toDateString() === $tanggal
                    );
                })
                ->when($status === 'in_stok', fn ($c) => $c->where('terjual', false))
                ->when($status === 'terjual', fn ($c) => $c->where('terjual', true));

            // ---------- SORT ----------
            $cmpKeluar = function ($a, $b, $dir) {
                if ($a->ts_keluar === null && $b->ts_keluar === null) return 0;
                if ($a->ts_keluar === null) return 1;
                if ($b->ts_keluar === null) return -1;
                return $dir === 'desc'
                    ? $b->ts_keluar <=> $a->ts_keluar
                    : $a->ts_keluar <=> $b->ts_keluar;
            };

            $rows = $rows->sort(function ($a, $b) use ($sort, $cmpKeluar) {
                return match ($sort) {
                    'masuk_terbaru'  => $b->ts_masuk <=> $a->ts_masuk,
                    'masuk_terlama'  => $a->ts_masuk <=> $b->ts_masuk,
                    'keluar_terbaru' => $cmpKeluar($a, $b, 'desc'),
                    'keluar_terlama' => $cmpKeluar($a, $b, 'asc'),
                    'az'             => strcasecmp($a->nama_gudang, $b->nama_gudang)
                                        ?: strcmp((string) $a->kode_stok, (string) $b->kode_stok),
                    'za'             => strcasecmp($b->nama_gudang, $a->nama_gudang)
                                        ?: strcmp((string) $b->kode_stok, (string) $a->kode_stok),
                    default          => ($a->terjual <=> $b->terjual)
                                        ?: ($a->ts_masuk <=> $b->ts_masuk),
                };
            })->values();

            // ---------- PAGINATION MANUAL ----------
            $page  = LengthAwarePaginator::resolveCurrentPage();
            $items = new LengthAwarePaginator(
                $rows->forPage($page, self::PER_PAGE)->values(),
                $rows->count(),
                self::PER_PAGE,
                $page,
                ['path' => $request->url()]
            );

            $items->withQueryString();

            return view('inventory.detailItem', compact('item', 'items', 'summary', 'q', 'tanggal', 'status', 'sort'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan detail barang');
        }
    }

    // ================================================================
    // EDIT BARANG (ITM) — STOK tidak diubah dari sini
    // ================================================================
    public function update(Request $request, string $id)
    {
        $decodedId = urldecode($id);
        $item = ITM::where('ITM', $decodedId)->firstOrFail();

        if ($request->has('HRG')) {
            $cleanHrg = preg_replace('/[^\d]/', '', (string) $request->input('HRG'));
            $request->merge(['HRG' => $cleanHrg !== '' ? (float) $cleanHrg : null]);
        }

        $data = $request->validateWithBag('editItem', [
            'ITM' => 'required|string|max:10',
            'DES' => 'required|string|max:150',
            'MRK' => 'required|string|max:10',
            'KTG' => 'required|string|max:10',
            'SPP' => 'required|string|max:10',
            'HRG' => 'required|numeric|min:0',
        ], [
            'DES.required' => 'Nama barang wajib diisi.',
            'DES.string'   => 'Nama barang harus berupa teks.',
            'DES.max'      => 'Nama barang maksimal 150 karakter.',
            'MRK.required' => 'Merk wajib dipilih.',
            'KTG.required' => 'Kategori wajib dipilih.',
            'SPP.required' => 'Supplier wajib dipilih.',
            'HRG.required' => 'Harga jual wajib diisi.',
            'HRG.numeric'  => 'Harga jual harus berupa angka.',
            'HRG.min'      => 'Harga jual minimal 0.',
        ]);

        if (ITM::where('DES', $data['DES'])
                ->where('MRK', $data['MRK'])
                ->where('KTG', $data['KTG'])
                ->where('ITM', '!=', $item->ITM)
                ->exists()) {
            throw ValidationException::withMessages([
                'DES' => 'Barang dengan nama, merk, dan kategori tersebut sudah ada.',
            ])->errorBag('editItem');
        }

        try {
            $item->update($data);

            return redirect()->route('inventory.item')->with('success', 'Barang berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui barang');
        }
    }

    // ================================================================
    // TAMBAH STOK (1 input = 1 baris ITMDT = ITM.STOK + 1)
    // Setiap simpan, kode BC Out ditambahkan ke array session 'stok_list'
    // ================================================================
    public function tambahStok(Request $request)
    {
        try {
            if ($request->has('stok_hrb')) {
                $cleanHrb = preg_replace('/[^\d]/', '', (string) $request->input('stok_hrb'));
                $request->merge(['stok_hrb' => $cleanHrb !== '' ? (float) $cleanHrb : null]);
            }

            $data = $request->validateWithBag('stok', [
                'stok_itm' => 'required|string|max:10',
                'stok_gd'  => 'required|string|max:10',
                'stok_hrb' => 'required|numeric|min:0',
                'stok_bc'  => 'required|string|max:20',
            ], [
                'stok_itm.required' => 'Pilih nama barang.',
                'stok_gd.required'  => 'Pilih gudang.',
                'stok_hrb.required' => 'Harga beli wajib diisi.',
                'stok_bc.required'  => 'Kode barcode wajib diisi.',
            ]);

            // Kalau list belum di-generate, barang tidak boleh diganti
            $listAktif = session('stok_list', []);

            if (!empty($listAktif) && $listAktif[0]['itm'] !== $data['stok_itm']) {
                return back()
                    ->withInput(['stok_itm' => $listAktif[0]['itm']] + $request->only(['stok_gd', 'stok_hrb']))
                    ->withErrors([
                        'stok_itm' => 'Masih ada ' . count($listAktif) . ' kode untuk "' . $listAktif[0]['des'] . '". Generate barcode dulu sebelum mengganti barang.',
                    ], 'stok');
            }

            if (!ITM::where('ITM', $data['stok_itm'])->exists()) {
                throw ValidationException::withMessages(['stok_itm' => 'Barang tidak ditemukan.'])
                    ->errorBag('stok');
            }

            if (!GD::where('GD', $data['stok_gd'])->exists()) {
                throw ValidationException::withMessages(['stok_gd' => 'Gudang tidak ditemukan.'])
                    ->errorBag('stok');
            }

            $bcOut = null;

            DB::transaction(function () use ($data, &$bcOut) {
                $bcOut = $this->generateBcOut($data['stok_itm']);

                ITMDT::create([
                    'ITM'    => $data['stok_itm'],
                    'BC_IN'  => $data['stok_bc'],
                    'BC_OUT' => $bcOut,
                    'TM'     => date('Y-m-d'),
                    'HRB'    => $data['stok_hrb'],
                    'GD'     => $data['stok_gd'],
                ]);

                ITM::where('ITM', $data['stok_itm'])->increment('STOK');
            });

            $namaItem   = ITM::where('ITM', $data['stok_itm'])->value('DES');
            $namaGudang = GD::where('GD', $data['stok_gd'])->value('DES');

            // Tambah ke array session (bertahan sampai di-generate)
            session()->push('stok_list', [
                'itm'    => $data['stok_itm'],
                'des'    => $namaItem,
                'gd'     => $namaGudang,
                'bc_in'  => $data['stok_bc'],
                'bc_out' => $bcOut,
            ]);

            // Pilihan barang, gudang & harga beli dipertahankan; hanya barcode yang dikosongkan
            return back()->withInput($request->only(['stok_itm', 'stok_gd', 'stok_hrb']));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan stok');
        }
    }

    // ================================================================
    // GENERATE: pindahkan list ke halaman print, lalu kosongkan list
    // ================================================================
    public function generate()
    {
        try {
            $list = session('stok_list', []);

            if (empty($list)) {
                return back()->withErrors(['stok_list' => 'Belum ada stok yang ditambahkan.'], 'stok');
            }

            session()->put('cetak_list', $list);
            session()->forget('stok_list');

            return redirect()->route('inventory.item.cetak');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika generate barcode');
        }
    }

    // ================================================================
    // HALAMAN PRINT
    // ================================================================
    public function cetak()
    {
        try {
            $cetak = session('cetak_list', []);

            if (empty($cetak)) {
                return redirect()->route('inventory.item');
            }

            return view('Inventory.cetakBarcode', ['cetak' => $cetak]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan halaman cetak');
        }
    }

    public function cetakSatuan(Request $request)
    {
        try {
            $bcOut = $request->input('bc_out') ?? $request->query('bc_out');

            if (!$bcOut) {
                return redirect()->back()->with('error', 'Kode barcode tidak ditemukan.');
            }

            $detail = ITMDT::where('BC_OUT', $bcOut)->first();
            if (!$detail) {
                return redirect()->back()->with('error', 'Data stok barang tidak ditemukan.');
            }

            $itm = ITM::where('ITM', $detail->ITM)->first();
            $gd  = GD::where('GD', $detail->GD)->first();

            $cetak = [
                [
                    'itm'    => $detail->ITM,
                    'des'    => $itm->DES ?? $detail->ITM,
                    'gd'     => $gd->DES ?? '-',
                    'bc_in'  => $detail->BC_IN,
                    'bc_out' => $detail->BC_OUT,
                ]
            ];

            return view('Inventory.cetakBarcode', ['cetak' => $cetak]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika mencetak barcode');
        }
    }

    /**
     * BC_OUT = ITM (maks 10) + yymmdd (6) + urutan 4 digit  => maks 20 karakter.
     */
    private function generateBcOut(string $itm): string
    {
        try {
            $prefix = strtoupper($itm) . date('ymd');
            $urut   = ITMDT::where('BC_OUT', 'like', $prefix . '%')->count();

            do {
                $urut++;
                $kode = $prefix . str_pad((string) $urut, 4, '0', STR_PAD_LEFT);
            } while (ITMDT::where('BC_OUT', $kode)->exists());

            return $kode;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function destroy($id)
    {
        try {
            $item = ITM::where('ITM', $id)->firstOrFail();
            $item->delete();

            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus data');
        }
    }
}