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
    }


    /**
     * Menyimpan sales baru.
     */
    public function store(Request $request)
    {
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
    }


    /**
     * Menampilkan data untuk edit.
     */
    public function edit($sls)
    {
        $sales = SLS::where('sls', $sls)->firstOrFail();

        return view('sales.edit', compact('sales'));
    }


    /**
     * Mengubah data sales.
     */
    public function update(Request $request, $sls)
    {
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
    }


    /**
     * Menghapus sales.
     */
    public function destroy($sls)
    {
        $sales = SLS::where('sls', $sls)->firstOrFail();

        $sales->delete();

        return redirect()
            ->route('POS.sales')
            ->with('success', 'Sales berhasil dihapus.');
    }
}