<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KTG;

class KTGController extends Controller
{
    // halaman index untuk menampilkan semua data gudang
    public function index()
    {
        $data = KTG::all();
        return view('data.kategori', compact('data'));
    }

    // halaman create untuk menampilkan form tambah data gudang
    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'KTG' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
        ]);

        KTG::create($validated);

        return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data kategori
    public function edit($ktg)
    {
        $ktg = KTG::findOrFail($ktg);
        return view('kategori.edit', compact('ktg'));
    }

    public function update(Request $request, $ktg)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:45',
        ]);

        $ktg = KTG::findOrFail($ktg);
        $ktg->update($validated);

        return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil diperbarui.');
    }

    public function destroy($ktg)
    {
        $ktg = KTG::findOrFail($ktg);
        $ktg->delete();

        return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil dihapus.');
    }
}
