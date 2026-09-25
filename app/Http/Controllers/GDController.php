<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GD;

class GDController extends Controller
{
    // halaman index untuk menampilkan semua data gudang
    public function index()
    {
        $data = GD::all();

        return view('data.gudang', compact('data'));
    }

    // halaman create untuk menampilkan form tambah data gudang
    public function create()
    {
        return view('gudang.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'GD' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
        ]);

        GD::create($validated);

        return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil ditambahkan.');
    }

    public function edit($gd)
    {
        $gd = GD::findOrFail($gd);
        return view('gudang.edit', compact('gd'));
    }

    public function update(Request $request, $gd)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:45',
        ]);

        $gd = GD::findOrFail($gd);
        $gd->update($validated);

        return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function destroy($gd)
    {
        $gd = GD::findOrFail($gd);
        $gd->delete();

        return redirect()->route('gudang.index')->with('success', 'Data gudang berhasil dihapus.');
    }
}
