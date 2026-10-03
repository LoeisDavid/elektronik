<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KTG;

class KTGController extends Controller
{
    // halaman index untuk menampilkan semua data gudang
    public function index(Request $request)
    {
        $query = KTG::query();

        if ($request->filled('keyword')) {
            $search = $request->keyword;
            $query->where('DES', 'LIKE', "%{$search}%")
                  ->orWhere('KTG', 'LIKE', "%{$search}%");
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
            'DES' => 'required|string|max:45',
        ]);

        KTG::create($validated);

        $validated['KTG'] = $this->generateCode($validated['DES'], 'KTG');

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
