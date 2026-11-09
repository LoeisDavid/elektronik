<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\STS;
use Illuminate\Support\Facades\DB;

class GDController extends Controller
{
    // halaman index untuk menampilkan semua data gudang
    public function index(Request $request)
    {
        try {
            // Stok = jumlah itmdt di gudang tsb yang BC_OUT-nya tidak ada di sts
            $stokSub = DB::table('itmdt')
                ->selectRaw('COUNT(*)')
                ->whereColumn('itmdt.GD', 'gd.GD')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                    ->from('sts')
                    ->whereColumn('sts.BC_OUT', 'itmdt.BC_OUT');
                });

            $query = GD::query()
                ->select('gd.*')
                ->selectSub($stokSub, 'stok');

            if ($request->filled('keyword')) {
                $search = $request->keyword;
                $query->where(function ($q) use ($search) {
                    $q->where('DES', 'LIKE', "%{$search}%")
                    ->orWhere('GD', 'LIKE', "%{$search}%");
                });
            }

            $sort = $request->sort;

            if ($sort == 'a-z') {
                $query->orderBy('DES', 'asc');
            } else if ($sort == 'z-a') {
                $query->orderBy('DES', 'desc');
            } else if ($sort == 'new-old') {
                $query->orderBy('created_at', 'desc');
            } else if ($sort == 'old-new') {
                $query->orderBy('created_at', 'asc');
            } else if ($sort == 'stok-sedikit') {
                $query->orderBy('stok', 'asc')->orderBy('DES', 'asc');
            } else {
                // default: stok terbanyak
                $query->orderBy('stok', 'desc')->orderBy('DES', 'asc');
            }

            $data = $query->paginate(10);

            return view('Inventory.gudang', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data gudang');
        }
    }

    // halaman create untuk menampilkan form tambah data gudang
    public function create()
    {
        return view('gudang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:45|unique:gd,DES',
        ], [
            'DES.required' => 'Nama gudang wajib diisi.',
            'DES.string'   => 'Nama gudang harus berupa teks.',
            'DES.max'      => 'Nama gudang maksimal 45 karakter.',
            'DES.unique'   => 'Nama gudang sudah terdaftar.',
        ]);

        try {
            $validated['GD'] = $this->generateCode($validated['DES'], 'GD');

            $gd = GD::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data gudang berhasil ditambahkan.',
                    'data'    => $gd,
                ]);
            }

            return redirect()->route('inventory.gudang')->with('success', 'Data gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan ketika menambahkan data gudang.',
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data gudang');
        }
    }

    public function edit($gd)
    {
        try {
            $gd = GD::findOrFail($gd);
            return view('gudang.edit', compact('gd'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan form edit gudang');
        }
    }

    // detail gudang + daftar barang di gudang yang belum terjual
    public function show(Request $request, $gd)
    {
        try {
            $gd = GD::findOrFail($gd);

            // Barang yang belum terjual di gudang ini
            $query = ITMDT::query()
                ->where('GD', $gd->GD)
                ->whereNotIn('BC_OUT', STS::select('BC_OUT'));

            // SEARCH: barcode, nama barang, atau kode ITM
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;

                $kodeItm = ITM::where('DES', 'LIKE', "%{$keyword}%")
                    ->orWhere('ITM', 'LIKE', "%{$keyword}%")
                    ->pluck('ITM');

                $query->where(function ($q) use ($keyword, $kodeItm) {
                    $q->where('BC_OUT', 'LIKE', "%{$keyword}%")
                        ->orWhereIn('ITM', $kodeItm);
                });
            }

            // SORT
            $sort = $request->input('sort', 'old-new');

            if (!in_array($sort, ['a-z', 'z-a', 'new-old', 'old-new'])) {
                $sort = 'old-new';
            }

            $itmTable = (new ITM)->getTable();
            $dtTable = (new ITMDT)->getTable();

            // Kelompokkan berdasarkan kode ITM
            // Satu kode ITM hanya menghasilkan satu baris
            $groupedQuery = (clone $query)
                ->select("{$dtTable}.ITM")
                ->selectRaw("MIN({$dtTable}.created_at) as first_date")
                ->selectRaw("MAX({$dtTable}.created_at) as last_date")
                ->groupBy("{$dtTable}.ITM");

            // Subquery untuk nama barang
            $namaBarang = ITM::select('DES')
                ->whereColumn(
                    "{$itmTable}.ITM",
                    "{$dtTable}.ITM"
                )
                ->limit(1);

            switch ($sort) {
                case 'a-z':
                    $groupedQuery->orderBy($namaBarang, 'asc');
                    break;

                case 'z-a':
                    $groupedQuery->orderBy($namaBarang, 'desc');
                    break;

                case 'new-old':
                    $groupedQuery->orderBy('last_date', 'desc');
                    break;

                default:
                    $groupedQuery->orderBy('first_date', 'asc');
                    break;
            }

            // Urutan kedua agar hasil stabil
            $groupedQuery->orderBy("{$dtTable}.ITM");

            // Pagination berdasarkan barang unik, bukan barcode
            $rows = $groupedQuery
                ->paginate(10)
                ->withQueryString();

            // Kode ITM pada halaman saat ini
            $kodeItmHalaman = $rows->getCollection()
                ->pluck('ITM')
                ->unique()
                ->values();

            // Hitung seluruh stok belum terjual per ITM di gudang ini
            $stokPerItm = ITMDT::query()
                ->where('GD', $gd->GD)
                ->whereNotIn('BC_OUT', STS::select('BC_OUT'))
                ->whereIn('ITM', $kodeItmHalaman)
                ->selectRaw('ITM, COUNT(*) as total')
                ->groupBy('ITM')
                ->pluck('total', 'ITM');

            // Ambil informasi barang dan jumlah stok
            $items = $rows->through(function ($row) use ($stokPerItm) {
                $itm = ITM::where('ITM', $row->ITM)->first();

                return (object) [
                    'DES' => $itm->DES ?? '-',
                    'MRK' => $itm?->MRK()?->DES ?? '-',
                    'KTG' => $itm?->KTG()?->DES ?? '-',
                    'ITM' => $row->ITM,
                    'QTY' => $stokPerItm[$row->ITM] ?? 0,
                ];
            });

            return view(
                'inventory.detailGudang',
                compact('gd', 'items', 'sort')
            );

        } catch (\Exception $e) {
            return redirect()->back()->with(
                'error',
                'Terjadi kesalahan ketika menampilkan detail gudang'
            );
        }

    }

    public function update(Request $request, $gd)
    {
        try {
            $validated = $request->validate([
                'DES' => 'required|string|max:45',
            ]);

            $gd = GD::findOrFail($gd);
            $gd->update($validated);

            return redirect()->route('inventory.gudang')->with('success', 'Data gudang berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui data gudang');
        }
    }

    public function destroy($gd)
    {
        try {
            $gd = GD::findOrFail($gd);
            $gd->delete();

            return redirect()->route('inventory.gudang')->with('success', 'Data gudang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus data gudang');
        }
    }
}