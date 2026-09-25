<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ITMDT;

class ITMDTController extends Controller
{
    // halaman index untuk menampilkan semua data merk
    public function index()
    {
        $data = ITMDT::all();
        return view('data.itmdt', compact('data'));
    }

    // halaman create untuk menampilkan form tambah data item detail
    public function create()
    {
        return view('itmdt.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'BC_OUT' => 'required|string|max:20',
            'ITM' => 'required|string|max:10',
            'BC_IN' => 'required|string|max:20',
            'TM' => 'required|date',
            'GD' => 'required|string|max:10',
        ]);

        ITMDT::create($validated);

        return redirect()->route('itmdt.index')->with('success', 'Data itmdt berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data merk
    public function edit($itmdt)
    {
        $itmdt = ITMDT::findOrFail($itmdt);
        return view('itmdt.edit', compact('mrk'));
    }

    public function update(Request $request, $itmdt)
    {
        $validated = $request->validate([
            'BC_OUT' => 'required|string|max:20',
            'ITM' => 'required|string|max:10',
            'BC_IN' => 'required|string|max:20',
            'TM' => 'required|date',
            'GD' => 'required|string|max:10',
        ]);

        $itmdt = ITMDT::findOrFail($itmdt);
        $itmdt->update($validated);

        return redirect()->route('itmdt.index')->with('success', 'Data itmdt berhasil diperbarui.');
    }

    public function destroy($itmdt)
    {
        $itmdt = ITMDT::findOrFail($itmdt);
        $itmdt->delete();

        return redirect()->route('itmdt.index')->with('success', 'Data itmdt berhasil dihapus.');
    }
}
