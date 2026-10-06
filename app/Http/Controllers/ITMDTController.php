<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ITMDT;

class ITMDTController extends Controller
{
    // halaman index untuk menampilkan semua data merk
    public function index()
    {
        try {
            $data = ITMDT::all();
            return view('data.itmdt', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data itmdt');
        }
    }

    // halaman create untuk menampilkan form tambah data item detail
    public function create()
    {
        try {
            return view('itmdt.create');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan form tambah data itmdt');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'BC_OUT' => 'required|string|max:20',
                'ITM' => 'required|string|max:10',
                'BC_IN' => 'required|string|max:20',
                'TM' => 'required|date',
                'GD' => 'required|string|max:10',
            ]);

            ITMDT::create($validated);

            return redirect()->route('itmdt.index')->with('success', 'Data itmdt berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data itmdt');
        }
    }

    // halaman edit untuk menampilkan form edit data merk
    public function edit($itmdt)
    {
        try {
            $itmdt = ITMDT::findOrFail($itmdt);
            return view('itmdt.edit', compact('mrk'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan form edit data itmdt');
        }
    }

    public function update(Request $request, $itmdt)
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui data itmdt');
        }
    }

    public function destroy($itmdt)
    {
        try {
            $itmdt = ITMDT::findOrFail($itmdt);
            $itmdt->delete();

            return redirect()->route('itmdt.index')->with('success', 'Data itmdt berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus data itmdt');
        }
    }
}