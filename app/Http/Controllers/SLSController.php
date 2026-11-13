<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SLS;

class SLSController extends Controller
{
    /**
     * Menampilkan data sales.
     */
    public function index(Request $request)
    {
        try {
            $query = SLS::query();

            if ($request->filled('keyword')) {
                $search = $request->keyword;
                $query->where('DES', 'LIKE', "%{$search}%")
                      ->orWhere('SLS', 'LIKE', "%{$search}%")
                      ->orWhere('HP', 'LIKE', "%{$search}%")
                      ->orWhere('AL', 'LIKE', "%{$search}%");
            }

            $sort = $request->sort;

            if($sort == 'a-z') {
                $query->orderBy('DES', 'asc');
            } else if ($sort == 'z-a') {
                $query->orderBy('DES', 'desc');
            } else if($sort == 'new-old') {
                $query->orderBy('created_at', 'desc');
            } else if ($sort == 'old-new') {
                $query->orderBy('created_at', 'asc');
            } else {
                $query->orderBy('DES', 'asc');
            }

            $data = $query->paginate(10)->withQueryString();

            return view('POS.sales', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data sales.');
        }
    }


    /**
     * Menyimpan sales baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:150',
            'HP'  => 'required|numeric|max_digits:20|unique:sls,HP',
            'AL'  => 'required|string|max:100',
        ],[
            'DES.required' => 'Nama Sales wajib diisi.',
            'DES.string'   => 'Nama Sales harus berupa teks.',
            'DES.max'      => 'Nama Sales maksimal 150 karakter.',
            'HP.required'  => 'Nomor HP wajib diisi.',
            'HP.numeric'   => 'Nomor HP harus berupa angka.',
            'HP.max_digits' => 'Nomor HP maksimal 20 digit.',
            'HP.unique'     => 'Nomor HP sudah digunakan.',
            'AL.required'  => 'Alamat wajib diisi.',
            'AL.string'    => 'Alamat harus berupa teks.',
            'AL.max'       => 'Alamat maksimal 100 karakter.',
        ]);
        try {
            $validated['SLS'] = $this->generateCode($validated['DES'], 'SLS');

            $sales = SLS::create($validated);

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sales berhasil ditambahkan.',
                    'data'    => $sales,
                ]);
            }

            return redirect()
                ->route('POS.sales')
                ->with('success', 'Sales berhasil ditambahkan.');
        } catch (\Exception $e) {
            report($e);
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menambahkan sales: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan sales.');
        }
    }


    /**
     * Menampilkan data untuk edit.
     */
    public function edit($sls)
    {
        try {
            $sales = SLS::where('sls', $sls)->firstOrFail();

            return view('sales.edit', compact('sales'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data sales.');
        }
    }


    /**
     * Mengubah data sales.
     */
    public function update(Request $request, $sls)
    {
        // Decode parameter ID untuk mengantisipasi karakter khusus
            $decodedId = urldecode($sls);

            $sales = SLS::where('sls', $decodedId)->firstOrFail();

            $validated = $request->validateWithBag('editSales', [
                'SLS' => 'required|string|max:10|unique:sls,SLS,' . $sales->SLS . ',SLS',
                'DES' => 'required|string|max:150',
                'HP'  => 'required|numeric|max_digits:20|unique:sls,HP,' . $sales->SLS . ',SLS',
                'AL'  => 'required|string|max:100',
            ],[
                'DES.required' => 'Nama Sales wajib diisi.',
                'DES.string'   => 'Nama Sales harus berupa teks.',
                'DES.max'      => 'Nama Sales maksimal 150 karakter.',
                'HP.required'  => 'Nomor HP wajib diisi.',
                'HP.numeric'   => 'Nomor HP harus berupa angka.',
                'HP.max_digits' => 'Nomor HP maksimal 20 digit.',
                'HP.unique'     => 'Nomor HP sudah digunakan.',
                'AL.required'  => 'Alamat wajib diisi.',
                'AL.string'    => 'Alamat harus berupa teks.',
                'AL.max'       => 'Alamat maksimal 100 karakter.',
            ]);
        try {

            $sales->update($validated);

            return redirect()
                ->route('POS.sales')
                ->with('success', 'Sales berhasil diubah.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika mengubah sales.');
        }
    }


    /**
     * Menghapus sales.
     */
    public function destroy($sls)
    {
        try {
            $sales = SLS::where('sls', $sls)->firstOrFail();

            $sales->delete();

            return redirect()
                ->route('POS.sales')
                ->with('success', 'Sales berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus sales.');
        }
    }
}