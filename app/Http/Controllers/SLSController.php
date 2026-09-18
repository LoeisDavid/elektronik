<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SLS;

class SLSController extends Controller
{
    // halaman index untuk menampilkan semua data gudang
    public function index()
    {
        $sls = SLS::all();
        return response()->json($sls);
    }

    // halaman create untuk menampilkan form tambah data gudang
    public function create()
    {
        return view('sales.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'SLS' => 'required|string|max:10',
            'DES' => 'required|string|max:150',
            'HP' => 'required|string|max:20',
            'AL' => 'required|string|max:100',
        ]);

        SLS::create($validated);

        return redirect()->route('sales.index')->with('success', 'Data sales berhasil ditambahkan.');
    }

    public function edit($sls)
    {
        $sls = SLS::findOrFail($sls);
        return view('sales.edit', compact('sls'));
    }

    public function update(Request $request, $sls)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:150',
            'HP' => 'required|string|max:20',
            'AL' => 'required|string|max:100',
        ]);

        $sls = SLS::findOrFail($sls);
        $sls->update($validated);

        return redirect()->route('sales.index')->with('success', 'Data sales berhasil diperbarui.');
    }

    public function destroy($sls)
    {
        $sls = SLS::findOrFail($sls);
        $sls->delete();

        return redirect()->route('sales.index')->with('success', 'Data sales berhasil dihapus.');
    }
}
