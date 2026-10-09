<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\STS;

class STSController extends Controller
{
    // halaman index untuk menampilkan semua data merk
    public function index(Request $request)
    {
        try {
            $query = STS::query();

            if ($request->filled('keyword')) {
                $search = $request->keyword;
                $query->where('DES', 'LIKE', "%{$search}%")
                      ->orWhere('STS', 'LIKE', "%{$search}%");
            }

            $sort = $request->filled('sort');

            if($sort == 'a-z') {
                $query->orderBy('DES', 'asc');
            } else if ($sort == 'z-a') {
                $query->orderBy('DES', 'desc');
            } else if($sort == 'new-old') {
                $query->orderBy('created_at', 'desc');
            } else if ($sort == 'old-new') {
                $query->orderBy('created_at', 'asc');
            } else {
                $query->orderBy('DES', 'asc');
            }

            $data = $query->paginate(10);
            return view('data.status', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data status.');
        }
    }

    // halaman create untuk menampilkan form tambah data item detail
    public function create()
    {
        try {
            return view('sts.create');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan form tambah status.');
        }
    }

    public function store(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data sts.');
        }
    }

    // halaman edit untuk menampilkan form edit data merk
    public function edit($sts)
    {
        try {
            $sts = STS::findOrFail($sts);
            return view('sts.edit', compact('sts'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data sts.');
        }
    }

    public function update(Request $request, $rec)
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui data sts.');
        }
    }

    public function destroy($rec)
    {
        try {
            $sts = STS::findOrFail($rec);
            $sts->delete();

            return redirect()->route('sts.index')->with('success', 'Data sts berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus data sts.');
        }
    }
}