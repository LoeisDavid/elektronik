<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ITM;

class ITMController extends Controller
{
     // halaman index untuk menampilkan semua data item
    public function index()
    {
        $data = ITM::all();
        return view('data.item', compact('data'));
    }

    // halaman create untuk menampilkan form tambah data item
    public function create()
    {
        return view('item.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ITM' => 'required|string|max:10',
            'DES' => 'required|string|max:150',
            'STOK' => 'required|integer',
            'HRG' => 'required|decimal:2',
            'KTG' => 'required|string|max:10',
            'MRK' => 'required|string|max:10',
            'SPP' => 'required|string|max:10',
        ]);

        ITM::create($validated);

        return redirect()->route('item.index')->with('success', 'Data item berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data item
    public function edit($itm)
    {
        $itm = ITM::findOrFail($itm);
        return view('item.edit', compact('itm'));
    }

    public function update(Request $request, $itm)
    {
        $validated = $request->validate([
            'ITM' => 'required|string|max:10',
            'DES' => 'required|string|max:150',
            'STOK' => 'required|integer',
            'HRG' => 'required|decimal:2',
            'KTG' => 'required|string|max:10',
            'MRK' => 'required|string|max:10',
            'SPP' => 'required|string|max:10',
        ]);

        $itm = ITM::findOrFail($itm);
        $itm->update($validated);

        return redirect()->route('item.index')->with('success', 'Data item berhasil diperbarui.');
    }

    public function destroy($itm)
    {
        $itm = ITM::findOrFail($itm);
        $itm->delete();

        return redirect()->route('item.index')->with('success', 'Data item berhasil dihapus.');
    }
}
