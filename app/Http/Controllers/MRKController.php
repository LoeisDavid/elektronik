<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MRK;

class MRKController extends Controller
{
    // halaman index untuk menampilkan semua data merk
    public function index()
    {
        $mrks = MRK::all();
        return response()->json($mrks);
    }

    // halaman create untuk menampilkan form tambah data merk
    public function create()
    {
        return view('merk.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'MRK' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
        ]);

        MRK::create($validated);

        return redirect()->route('merk.index')->with('success', 'Data merk berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data merk
    public function edit($mrk)
    {
        $mrk = MRK::findOrFail($mrk);
        return view('merk.edit', compact('mrk'));
    }

    public function update(Request $request, $mrk)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:45',
        ]);

        $mrk = MRK::findOrFail($mrk);
        $mrk->update($validated);

        return redirect()->route('merk.index')->with('success', 'Data merk berhasil diperbarui.');
    }

    public function destroy($mrk)
    {
        $mrk = MRK::findOrFail($mrk);
        $mrk->delete();

        return redirect()->route('merk.index')->with('success', 'Data merk berhasil dihapus.');
    }
}
