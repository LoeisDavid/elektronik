<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ACR;

class ACRController extends Controller
{
     // halaman index untuk menampilkan semua data acr
    public function index()
    {
        $acrs = ACR::all();
        return response()->json($acrs);
    }

    // halaman create untuk menampilkan form tambah data acr
    public function create()
    {
        return view('acr.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'REF_NO' => 'required|string|max:10',
            'DT' => 'required|date',
            'CST' => 'required|string|max:10',
            'SLS' => 'required|string|max:10',
            'BRT' => 'required|double',
            'POT_1' => 'nullable|integer',
            'POT_2' => 'nullable|integer',
            'POT_R' => 'nullable|decimal:2',
            'USER' => 'required|string|max:10',
        ]);

        ACR::create($validated);

        return redirect()->route('acr.index')->with('success', 'Data acr berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data acr
    public function edit($ref_no)
    {
        $acr = ACR::findOrFail($ref_no);
        return view('acr.edit', compact('acr'));
    }

    public function update(Request $request, $ref_no)
    {
        $validated = $request->validate([
            'REF_NO' => 'required|string|max:10',
            'DT' => 'required|date',
            'CST' => 'required|string|max:10',
            'SLS' => 'required|string|max:10',
            'BRT' => 'required|double',
            'POT_1' => 'nullable|integer',
            'POT_2' => 'nullable|integer',
            'POT_R' => 'nullable|decimal:2',
            'USER' => 'required|string|max:10',
        ]);

        $acr = ACR::findOrFail($ref_no);
        $acr->update($validated);

        return redirect()->route('acr.index')->with('success', 'Data acr berhasil diperbarui.');
    }

    public function destroy($ref_no)
    {
        $acr = ACR::findOrFail($ref_no);
        $acr->delete();

        return redirect()->route('acr.index')->with('success', 'Data acr berhasil dihapus.');
    }
}
