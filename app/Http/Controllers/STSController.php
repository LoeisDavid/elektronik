<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\STS;

class STSController extends Controller
{
    // halaman index untuk menampilkan semua data merk
    public function index()
    {
        $data = STS::all();
        return view('data.status', compact('data'));
    }

    // halaman create untuk menampilkan form tambah data item detail
    public function create()
    {
        return view('sts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'REC' => 'required|string|max:3',
            'REF_NO' => 'required|string|max:10',
            'BC_OUT' => 'required|string|max:20',
            'ITM' => 'required|string|max:10',
            'HGJ' => 'required|decimal',
            'ST' => 'required|string|max:3',
            'POT_1' => 'nullable|integer',
            'POT_2' => 'nullable|integer',
            'POT_R' => 'nullable|decimal',
            'PPN' => 'nullable|integer',
            'BRT' => 'required|decimal',
        ]);

        STS::create($validated);

        return redirect()->route('sts.index')->with('success', 'Data sts berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data merk
    public function edit($sts)
    {
        $sts = STS::findOrFail($sts);
        return view('sts.edit', compact('sts'));
    }

    public function update(Request $request, $rec)
    {
        $validated = $request->validate([
            'REC' => 'required|string|max:3',
            'REF_NO' => 'required|string|max:10',
            'BC_OUT' => 'required|string|max:20',
            'ITM' => 'required|string|max:10',
            'HGJ' => 'required|decimal',
            'ST' => 'required|string|max:3',
            'POT_1' => 'nullable|integer',
            'POT_2' => 'nullable|integer',
            'POT_R' => 'nullable|decimal',
            'PPN' => 'nullable|integer',
            'BRT' => 'required|decimal',
        ]);

        $sts = STS::findOrFail($rec);
        $sts->update($validated);

        return redirect()->route('sts.index')->with('success', 'Data sts berhasil diperbarui.');
    }

    public function destroy($rec)
    {
        $sts = STS::findOrFail($rec);
        $sts->delete();

        return redirect()->route('sts.index')->with('success', 'Data sts berhasil dihapus.');
    }
}
