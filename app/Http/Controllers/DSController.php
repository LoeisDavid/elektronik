<?php

namespace App\Http\Controllers;

use App\Models\DS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class DSController extends Controller
{
    /**
     * Halaman index untuk menampilkan data desa beserta total transaksi dan total pembelian.
     * Relasi: ds -> cst -> acr -> sts.
     */
    public function index(Request $request)
    {
        try {
            // Mengambil data desa dengan akumulasi transaksi via cst -> acr -> sts
            $baseQuery = DB::table('ds')
                ->leftJoin('cst', 'cst.DS', '=', 'ds.DS')
                ->leftJoin('acr', 'acr.CST', '=', 'cst.CST')
                ->leftJoin('sts', 'sts.REF_NO', '=', 'acr.REF_NO')
                ->select(
                    'ds.DS',
                    'ds.DES',
                    'ds.created_at',
                    DB::raw('COUNT(DISTINCT acr.REF_NO) as total_transaksi'),
                    DB::raw('COALESCE(SUM(sts.BRT), 0) as total_nominal')
                )
                ->groupBy('ds.DS', 'ds.DES', 'ds.created_at');

            $query = DS::fromSub($baseQuery, 'ds');

            // -------------------------------------------------------------
            // SEARCH: Nama desa, banyaknya transaksi, dan nominalnya
            // -------------------------------------------------------------
            if ($request->filled('keyword')) {
                $rawKeyword = trim((string) $request->keyword);
                $cleanDigits = preg_replace('/[^0-9]/', '', $rawKeyword);

                $query->where(function ($q) use ($rawKeyword, $cleanDigits) {
                    $q->where('DES', 'LIKE', "%{$rawKeyword}%")
                      ->orWhere('DS', 'LIKE', "%{$rawKeyword}%");

                    // Pencarian banyaknya transaksi (jika keyword numerik murni)
                    if (is_numeric($rawKeyword)) {
                        $q->orWhere('total_transaksi', (int) $rawKeyword);
                    }

                    // Pencarian nominal
                    if ($cleanDigits !== '') {
                        $q->orWhere('total_nominal', 'LIKE', "%{$cleanDigits}%");
                    }
                });
            }

            // -------------------------------------------------------------
            // FILTER / SORT: Nama desa, banyaknya transaksi, dan nominalnya
            // -------------------------------------------------------------
            $sort = $request->query('sort', 'a-z');

            if ($sort === 'a-z') {
                $query->orderBy('DES', 'asc');
            } elseif ($sort === 'z-a') {
                $query->orderBy('DES', 'desc');
            } elseif ($sort === 'terbanyak' || $sort === 'transaksi-banyak') {
                $query->orderBy('total_transaksi', 'desc')->orderBy('total_nominal', 'desc');
            } elseif ($sort === 'terdikit' || $sort === 'transaksi-sedikit') {
                $query->orderBy('total_transaksi', 'asc')->orderBy('total_nominal', 'asc');
            } elseif ($sort === 'nominal-banyak' || $sort === 'termahal') {
                $query->orderBy('total_nominal', 'desc')->orderBy('total_transaksi', 'desc');
            } elseif ($sort === 'nominal-sedikit' || $sort === 'termurah') {
                $query->orderBy('total_nominal', 'asc')->orderBy('total_transaksi', 'asc');
            } else {
                $query->orderBy('DES', 'asc');
            }

            $data = $query->paginate(10)->withQueryString();

            return view('POS.desa', compact('data', 'sort'));
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data desa.');
        }
    }

    /**
     * Simpan desa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:50|unique:ds,DES',
        ], [
            'DES.required' => 'Nama Desa wajib diisi.',
            'DES.string'   => 'Nama Desa harus berupa teks.',
            'DES.max'      => 'Nama Desa maksimal 50 karakter.',
            'DES.unique'   => 'Nama Desa sudah terdaftar.',
        ]);

        try {
            $validated['DS'] = $this->generateCode($validated['DES'], 'ds');

            DS::create($validated);

            return redirect()->route('POS.desa')->with('success', 'Data desa berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data desa.');
        }
    }

    /**
     * Perbarui nama desa.
     */
    public function update(Request $request, string $id)
    {
        $decodedId = urldecode($id);
        $desa = DS::where('DS', $decodedId)->firstOrFail();

        $validated = $request->validateWithBag('editDesa', [
            'DES' => 'required|string|max:50|unique:ds,DES,' . $desa->DS . ',DS',
        ], [
            'DES.required' => 'Nama Desa wajib diisi.',
            'DES.string'   => 'Nama Desa harus berupa teks.',
            'DES.max'      => 'Nama Desa maksimal 50 karakter.',
            'DES.unique'   => 'Nama Desa sudah terdaftar.',
        ]);

        try {
            $desa->update([
                'DES' => $validated['DES'],
            ]);

            return redirect()->route('POS.desa')->with('success', 'Data desa berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui data desa.');
        }
    }

    /**
     * Hapus desa.
     */
    public function destroy(string $id)
    {
        $decodedId = urldecode($id);
        $desa = DS::where('DS', $decodedId)->firstOrFail();

        try {
            $desa->delete();
            return redirect()->route('POS.desa')->with('success', 'Data desa berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('POS.desa')->with('error', 'Data desa tidak dapat dihapus karena masih digunakan.');
        } catch (\Exception $e) {
            return redirect()->route('POS.desa')->with('error', 'Terjadi kesalahan ketika menghapus data desa.');
        }
    }

    /**
     * Halaman cetak laporan penjualan per desa (sesuai SS Frame 4).
     */
    public function cetak(Request $request)
    {
        try {
            $desaList = DB::table('ds')
                ->leftJoin('cst', 'cst.DS', '=', 'ds.DS')
                ->leftJoin('acr', 'acr.CST', '=', 'cst.CST')
                ->leftJoin('sts', 'sts.REF_NO', '=', 'acr.REF_NO')
                ->select(
                    'ds.DS',
                    'ds.DES',
                    DB::raw('COUNT(DISTINCT acr.REF_NO) as total_transaksi'),
                    DB::raw('COALESCE(SUM(sts.BRT), 0) as total_nominal')
                )
                ->groupBy('ds.DS', 'ds.DES')
                ->orderBy('total_nominal', 'desc')
                ->get();

            // Top 5 Total Pembelian (Nominal)
            $top5Nominal = $desaList->sortByDesc('total_nominal')->take(5)->values();
            $maxNominal = $top5Nominal->max('total_nominal') ?: 1;

            // Top 5 Frekuensi (Transaksi)
            $top5Frekuensi = $desaList->sortByDesc('total_transaksi')->take(5)->values();
            $maxFrekuensi = $top5Frekuensi->max('total_transaksi') ?: 1;

            // Identifikasi desa dengan nominal tertinggi dan frekuensi tertinggi
            $desaNominalTertinggi = $top5Nominal->first()?->DS;
            $desaFrekuensiTertinggi = $top5Frekuensi->first()?->DS;

            $petugas = auth()->user()?->USERNAME ?? 'admin';
            $tanggal = now()->locale('id')->translatedFormat('d F Y');
            $lokasi  = 'Sumenep';

            return view('POS.desa-print', compact(
                'desaList',
                'top5Nominal',
                'maxNominal',
                'top5Frekuensi',
                'maxFrekuensi',
                'desaNominalTertinggi',
                'desaFrekuensiTertinggi',
                'petugas',
                'tanggal',
                'lokasi'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat halaman cetak laporan desa: ' . $e->getMessage());
        }
    }
}
