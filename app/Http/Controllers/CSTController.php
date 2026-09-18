<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CST;

class CSTController extends Controller
{
     // halaman index untuk menampilkan semua data customer
    public function index()
    {
        $csts = CST::all();
        return response()->json($csts);
    }

    // halaman create untuk menampilkan form tambah data customer
    public function create()
    {
        return view('customer.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'CST' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
            'TLP' => 'required|string|max:20',
        ]);

        CST::create($validated);

        return redirect()->route('customer.index')->with('success', 'Data customer berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data customer
    public function edit($cst)
    {
        $cst = CST::findOrFail($cst);
        return view('customer.edit', compact('cst'));
    }

    public function update(Request $request, $cst)
    {
        $validated = $request->validate([
            'CST' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
            'TLP' => 'required|string|max:20',
        ]);

        $cst = CST::findOrFail($cst);
        $cst->update($validated);

        return redirect()->route('customer.index')->with('success', 'Data customer berhasil diperbarui.');
    }

    public function destroy($cst)
    {
        $cst = CST::findOrFail($cst);
        $cst->delete();

        return redirect()->route('customer.index')->with('success', 'Data customer berhasil dihapus.');
    }
}
