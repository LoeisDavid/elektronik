<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KTG;
use Illuminate\Database\QueryException;

class KTGController extends Controller
{
    // halaman index untuk menampilkan semua data gudang
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $sort    = $request->query('sort') === 'desc' ? 'desc' : 'asc';
 
        $data = KTG::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('KTG', 'like', "%{$keyword}%")
                      ->orWhere('DES', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('DES', $sort)
            ->paginate(10)
            ->withQueryString();   // keyword & sort tetap terbawa saat pindah halaman
 
        return view('inventory.kategori', compact('data', 'sort'));
    }
 
    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:45',
        ]);
 
        KTG::create($validated);

        $validated['KTG'] = $this->generateCode($validated['DES'], 'KTG');

        return redirect()->route('kategori.index')->with('success', 'Data kategori berhasil ditambahkan.');
    }
 
    /**
     * Perbarui nama kategori (kode kategori tidak bisa diubah).
     */
    public function update(Request $request, string $ktg)
    {
        $kategori = KTG::findOrFail($ktg);
 
        $validated = $request->validate([
            'DES' => ['required', 'string', 'max:255'],
        ], [
            'DES.required' => 'Nama kategori wajib diisi.',
        ]);
 
        $kategori->update($validated);
 
        return redirect()
            ->route('inventory.kategori')->with('success', 'Kategori berhasil diperbarui.');
    }
 
    /**
     * Hapus kategori.
     */
    public function destroy(string $ktg)
    {
        $kategori = KTG::findOrFail($ktg);
 
        try {
            $kategori->delete();
        } catch (QueryException $e) {
            // Biasanya karena kategori masih dipakai tabel lain (foreign key)
            return redirect()
                ->route('inventory.kategori');
        }
 
        return redirect()
            ->route('inventory.kategori')->with('success', 'Kategori berhasil dihapus.');
    }
}
