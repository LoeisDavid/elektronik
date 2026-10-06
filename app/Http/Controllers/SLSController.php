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
                      ->orWhere('SLS', 'LIKE', "%{$search}%");
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
        try {
            $validated = $request->validate([
                'DES' => 'required|string|max:150',
                'HP'  => 'required|string|max:20',
                'AL'  => 'required|string|max:100',
            ]);

            $validated['SLS'] = $this->generateCode($validated['DES'], 'SLS');

            SLS::create($validated);

            return redirect()
                ->route('POS.sales')
                ->with('success', 'Sales berhasil ditambahkan.');
        } catch (\Exception $e) {
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
        try {
            // Decode parameter ID untuk mengantisipasi karakter khusus
            $decodedId = urldecode($sls);

            $sales = SLS::where('sls', $decodedId)->firstOrFail();

            $validated = $request->validate([
                'SLS' => 'required|string|max:10|unique:sls,SLS,' . $sales->SLS . ',sls',
                'DES' => 'required|string|max:150',
                'HP'  => 'required|string|max:20',
                'AL'  => 'required|string|max:100',
            ]);

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