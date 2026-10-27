<?php

namespace App\Http\Controllers;

use App\Models\CST;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CSTController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = CST::query();

            // Filter Tipe: all, customer, desa
            $tipe = $request->query('tipe', 'all');
            if ($tipe === 'customer') {
                $query->whereNotNull('DS');
            } elseif ($tipe === 'desa') {
                $query->whereNull('DS');
            }

            if ($request->filled('keyword')) {
                $keyword = trim((string) $request->keyword);
                $query->where(function ($q) use ($keyword) {
                    $q->where('DES', 'LIKE', "%{$keyword}%")
                        ->orWhere('CST', 'LIKE', "%{$keyword}%")
                        ->orWhere('TLP', 'LIKE', "%{$keyword}%")
                        ->orWhere('AL', 'LIKE', "%{$keyword}%")
                        ->orWhereExists(function ($sub) use ($keyword) {
                            $sub->select(DB::raw(1))
                                ->from('cst as desa')
                                ->whereColumn('desa.CST', 'cst.DS')
                                ->where('desa.DES', 'LIKE', "%{$keyword}%");
                        });
                });
            }

            $sort = $request->sort;

            if ($sort === 'a-z') {
                $query->orderBy('DES', 'asc');
            } elseif ($sort === 'z-a') {
                $query->orderBy('DES', 'desc');
            } elseif ($sort === 'new-old') {
                $query->orderBy('created_at', 'desc');
            } elseif ($sort === 'old-new') {
                $query->orderBy('created_at', 'asc');
            } else {
                $query->orderBy('DES', 'asc');
            }

            $data = $query->paginate(10)->withQueryString();
            $desaList = CST::whereNull('DS')->orderBy('DES', 'asc')->get(['CST', 'DES']);

            return view('POS.customer', compact('data', 'desaList', 'sort', 'tipe'));
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data customer');
        }
    }

    public function store(Request $request)
    {
        $isDesa = $request->input('tipe') === 'desa';

        if ($isDesa) {
            $validated = $request->validate([
                'DES' => 'required|string|max:150|unique:cst,DES',
            ], [
                'DES.required' => 'Nama Desa wajib diisi.',
                'DES.string'   => 'Nama Desa harus berupa teks.',
                'DES.max'      => 'Nama Desa maksimal 150 karakter.',
                'DES.unique'   => 'Nama Desa sudah terdaftar.',
            ]);

            try {
                $code = $this->generateCode($validated['DES'], 'CST');

                $cst = CST::create([
                    'CST' => $code,
                    'DES' => $validated['DES'],
                    'TLP' => null,
                    'AL'  => null,
                    'DS'  => null,
                ]);

                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Desa berhasil ditambahkan.',
                        'data'    => $cst,
                        'is_desa' => true,
                    ]);
                }

                return redirect()->route('POS.customer')->with('success', 'Desa berhasil ditambahkan.');
            } catch (\Exception $e) {
                report($e);
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Gagal menambahkan desa: ' . $e->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data desa');
            }
        } else {
            $validated = $request->validate([
                'DES' => 'required|string|max:150|unique:cst,DES',
                'TLP' => 'required|numeric|max_digits:20|unique:cst,TLP',
                'AL'  => 'required|string|max:100',
                'DS'  => 'required|string|max:15|exists:cst,CST',
            ], [
                'DES.required'   => 'Nama Customer wajib diisi.',
                'DES.string'     => 'Nama Customer harus berupa teks.',
                'DES.max'        => 'Nama Customer maksimal 150 karakter.',
                'DES.unique'     => 'Nama Customer sudah terdaftar.',
                'TLP.required'   => 'Nomor Telepon wajib diisi.',
                'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
                'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
                'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
                'AL.required'    => 'Alamat wajib diisi.',
                'AL.string'      => 'Alamat harus berupa teks.',
                'AL.max'         => 'Alamat maksimal 100 karakter.',
                'DS.required'    => 'Desa wajib dipilih.',
                'DS.exists'      => 'Desa yang dipilih tidak valid.',
            ]);

            try {
                $validated['CST'] = $this->generateCode($validated['DES'], 'CST');

                $cst = CST::create($validated);

                if ($request->wantsJson() || $request->ajax()) {
                    $desaObj = CST::where('CST', $cst->DS)->first();
                    return response()->json([
                        'success' => true,
                        'message' => 'Customer berhasil ditambahkan.',
                        'data'    => array_merge($cst->toArray(), [
                            'desa_nama' => $desaObj?->DES ?? '',
                        ]),
                        'is_desa' => false,
                    ]);
                }

                return redirect()->route('POS.customer')->with('success', 'Customer berhasil ditambahkan.');
            } catch (\Exception $e) {
                report($e);
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Gagal menambahkan customer: ' . $e->getMessage()], 500);
                }
                return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data customer');
            }
        }
    }

    public function update(Request $request, $id)
    {
        $decodedId = urldecode($id);
        $customer = CST::where('CST', $decodedId)->firstOrFail();

        $isDesa = $request->input('tipe') === 'desa' || ($request->missing('tipe') && empty($customer->DS));

        if ($isDesa) {
            $validated = $request->validateWithBag('editCustomer', [
                'DES' => 'required|string|max:150|unique:cst,DES,' . $customer->CST . ',CST',
            ], [
                'DES.required' => 'Nama Desa wajib diisi.',
                'DES.string'   => 'Nama Desa harus berupa teks.',
                'DES.max'      => 'Nama Desa maksimal 150 karakter.',
                'DES.unique'   => 'Nama Desa sudah terdaftar.',
            ]);

            try {
                $customer->update([
                    'DES' => $validated['DES'],
                    'TLP' => null,
                    'AL'  => null,
                    'DS'  => null,
                ]);

                return redirect()->route('POS.customer')->with('success', 'Data desa berhasil diperbarui.');
            } catch (\Exception $e) {
                report($e);
                return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui data desa');
            }
        } else {
            $validated = $request->validateWithBag('editCustomer', [
                'DES' => 'required|string|max:150|unique:cst,DES,' . $customer->CST . ',CST',
                'TLP' => 'required|numeric|max_digits:20|unique:cst,TLP,' . $customer->CST . ',CST',
                'AL'  => 'required|string|max:100',
                'DS'  => 'required|string|max:15|exists:cst,CST',
            ], [
                'DES.required'   => 'Nama Customer wajib diisi.',
                'DES.string'     => 'Nama Customer harus berupa teks.',
                'DES.max'        => 'Nama Customer maksimal 150 karakter.',
                'DES.unique'     => 'Nama Customer sudah terdaftar.',
                'TLP.required'   => 'Nomor Telepon wajib diisi.',
                'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
                'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
                'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
                'AL.required'    => 'Alamat wajib diisi.',
                'AL.string'      => 'Alamat harus berupa teks.',
                'AL.max'         => 'Alamat maksimal 100 karakter.',
                'DS.required'    => 'Desa wajib dipilih.',
                'DS.exists'      => 'Desa yang dipilih tidak valid.',
            ]);

            try {
                $customer->update($validated);
                return redirect()->route('POS.customer')->with('success', 'Customer berhasil diubah.');
            } catch (\Exception $e) {
                report($e);
                return redirect()->back()->with('error', 'Terjadi kesalahan ketika mengupdate data customer');
            }
        }
    }

    public function destroy($id)
    {
        try {
            $customer = CST::where('CST', $id)->firstOrFail();
            if (empty($customer->DS)) {
                // If this is a desa, check if any customer is registered in this desa
                $isUsed = CST::where('DS', $customer->CST)->exists();
                if ($isUsed) {
                    return redirect()->back()->with('error', 'Desa tidak dapat dihapus karena masih digunakan oleh customer.');
                }
            }
            $customer->delete();
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus data customer');
        }
    }

    /**
     * Cetak Laporan Penjualan Per Desa (Fokus Pada Pembelian & Frekuensi Terdikit)
     */
    public function cetakLaporanDesa(Request $request)
    {
        try {
            $desaList = DB::table('cst as desa')
                ->whereNull('desa.DS')
                ->leftJoin('cst as customer', 'customer.DS', '=', 'desa.CST')
                ->leftJoin('acr', 'acr.CST', '=', 'customer.CST')
                ->leftJoin('sts', 'sts.REF_NO', '=', 'acr.REF_NO')
                ->select(
                    'desa.CST as DS',
                    'desa.DES',
                    DB::raw('COUNT(DISTINCT acr.REF_NO) as total_transaksi'),
                    DB::raw('COALESCE(SUM(sts.BRT), 0) as total_nominal')
                )
                ->groupBy('desa.CST', 'desa.DES')
                ->orderBy('total_nominal', 'asc')
                ->orderBy('total_transaksi', 'asc')
                ->get();

            // 5 Desa Pembelian Terdikit (Nominal)
            $top5Nominal = $desaList->sortBy('total_nominal')->take(5)->values();
            $maxNominal = $desaList->max('total_nominal') ?: 1;

            // 5 Desa Frekuensi Terdikit (Transaksi)
            $top5Frekuensi = $desaList->sortBy('total_transaksi')->take(5)->values();
            $maxFrekuensi = $desaList->max('total_transaksi') ?: 1;

            // Identifikasi desa dengan nominal terdikit dan frekuensi terdikit
            $desaNominalTerdikit = $top5Nominal->first()?->DS;
            $desaFrekuensiTerdikit = $top5Frekuensi->first()?->DS;

            $petugas = auth()->user()?->USERNAME ?? 'admin';
            $tanggal = now()->locale('id')->translatedFormat('d F Y');
            $lokasi  = 'Sumenep';

            $pdf = Pdf::loadView('POS.desa-print', compact(
                'desaList',
                'top5Nominal',
                'maxNominal',
                'top5Frekuensi',
                'maxFrekuensi',
                'desaNominalTerdikit',
                'desaFrekuensiTerdikit',
                'petugas',
                'tanggal',
                'lokasi'
            ))->setPaper('a4', 'portrait');

            return $pdf->stream('laporan-desa-terdikit-' . date('YmdHis') . '.pdf');
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Gagal mencetak laporan desa: ' . $e->getMessage());
        }
    }
}