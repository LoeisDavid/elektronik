<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPP;

class SPPController extends Controller
{
     // halaman index untuk menampilkan semua data supplier
    public function index()
    {
        $spps = SPP::all();
        return response()->json($spps);
    }

    // halaman create untuk menampilkan form tambah data supplier
    public function create()
    {
        return view('supplier.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'SPP' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
            'TLP' => 'required|string|max:20',
        ]);

        SPP::create($validated);

        return redirect()->route('merk.index')->with('success', 'Data merk berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data supplier
    public function edit($spp)
    {
        $spp = SPP::findOrFail($spp);
        return view('supplier.edit', compact('spp'));
    }

    public function update(Request $request, $spp)
    {
        $validated = $request->validate([
            'SPP' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
            'TLP' => 'required|string|max:20',
        ]);

        $spp = SPP::findOrFail($spp);
        $spp->update($validated);

        return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy($spp)
    {
        $spp = SPP::findOrFail($spp);
        $spp->delete();

        return redirect()->route('supplier.index')->with('success', 'Data supplier berhasil dihapus.');
    }
}
