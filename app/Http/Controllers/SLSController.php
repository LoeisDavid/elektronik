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
        $search = $request->search;

        $sales = SLS::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('sls', 'like', '%' . $search . '%')
                        ->orWhere('DES', 'like', '%' . $search . '%')
                        ->orWhere('HP', 'like', '%' . $search . '%')
                        ->orWhere('AL', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('sls')
            ->paginate(10)
            ->withQueryString();

        return view('data.sales', compact('sales'));
    }


    /**
     * Menyimpan sales baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sls' => 'required|string|max:10|unique:sales,sls',
            'DES' => 'required|string|max:150',
            'HP'  => 'required|string|max:20',
            'AL'  => 'required|string|max:100',
        ]);

        SLS::create($validated);

        return redirect()
            ->route('sales.index')
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
        $sales = SLS::where('sls', $sls)->firstOrFail();

        $validated = $request->validate([
            'sls' => 'required|string|max:10|unique:sales,sls,' . $sales->sls . ',sls',
            'DES' => 'required|string|max:150',
            'HP'  => 'required|string|max:20',
            'AL'  => 'required|string|max:100',
        ]);

        $sales->update($validated);

        return redirect()
            ->route('sales.index')
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
            ->route('sales.index')
            ->with('success', 'Sales berhasil dihapus.');
    }
}