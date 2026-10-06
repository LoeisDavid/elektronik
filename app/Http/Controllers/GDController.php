<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\STS;

class GDController extends Controller
{
    // halaman index untuk menampilkan semua data gudang
    public function index(Request $request)
    {
        try {
            $query = GD::query();

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
            } else {
                $query->orderBy('DES', 'asc');
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
        try {
            $validated = $request->validate([
                'DES' => 'required|string|max:45',
            ]);

            $validated['GD'] = $this->generateCode($validated['DES'], 'GD');

            GD::create($validated);

            return redirect()->route('inventory.gudang')->with('success', 'Data gudang berhasil ditambahkan.');
        } catch (\Exception $e) {
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

    // detail gudang
    public function show($gd)
    {
        try {
            $gd = GD::findOrFail($gd);
            return view('inventory.detailGudang', compact('gd'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan detail gudang');
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

    // daftar barang di gudang yang belum terjual
    public function daftarBarang(Request $request, $gd)
    {
        try{
            $gd = GD::findOrFail($gd);

            // Belum terjual = ada di itmdt, TIDAK ada di sts (dicek lewat BC_OUT)
            $query = ITMDT::query()
                ->where('GD', $gd->GD)
                ->whereNotIn('BC_OUT', STS::select('BC_OUT'));

            // Cari berdasarkan BC_OUT, atau nama/kode barang (nama ada di tabel ITM)
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

            $rows = $query
                ->orderBy('BC_OUT')
                ->paginate(10)
                ->withQueryString();

            // Lengkapi data barang lewat function itm(), bukan relasi
            $items = $rows->through(function ($row) {
                $itm = $row->itm();

                return (object) [
                    'BC_OUT' => $row->BC_OUT,
                    'DES'    => $itm->DES ?? '-',
                    'KTG'    => $itm->KTG ?? '-',
                    'SPP'    => $itm->SPP ?? '-',
                    'MRK'    => $itm->MRK ?? '-',
                    'HRB'    => $row->HRB ?? 0,
                ];
            });

            return view('Inventory.daftarBarang', compact('gd', 'items'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan daftar barang di gudang');
        }
    }
}