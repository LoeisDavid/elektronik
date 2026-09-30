<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MRK;

class MRKController extends Controller
{
    // halaman index untuk menampilkan semua data merk
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('keyword', ''));
        $sort    = $request->query('sort') === 'desc' ? 'desc' : 'asc';
 
        $data = MRK::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('MRK', 'like', "%{$keyword}%")
                      ->orWhere('DES', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('DES', $sort)
            ->paginate(10)
            ->withQueryString();   // keyword & sort tetap terbawa saat pindah halaman
 
        return view('inventory.merk', compact('data', 'sort'));
    }
 
    /**
     * Simpan merk baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'MRK' => ['required', 'string', 'max:10', 'unique:mrk,MRK'],
            'DES' => ['required', 'string', 'max:255'],
        ], [
            'MRK.required' => 'Kode merk wajib diisi.',
            'MRK.unique'   => 'Kode merk sudah digunakan.',
            'MRK.max'      => 'Kode merk maksimal 10 karakter.',
            'DES.required' => 'Nama merk wajib diisi.',
        ]);
 
        MRK::create($validated);
 
        return redirect()
            ->route('inventory.merk')
            ->with('success', 'Merk berhasil ditambahkan.');
    }
 
    /**
     * Perbarui nama merk (kode merk tidak bisa diubah).
     */
    public function update(Request $request, string $mrk)
    {
        $merk = MRK::findOrFail($mrk);
 
        $validated = $request->validate([
            'DES' => ['required', 'string', 'max:255'],
        ], [
            'DES.required' => 'Nama merk wajib diisi.',
        ]);
 
        $merk->update($validated);
 
        return redirect()
            ->route('inventory.merk')
            ->with('success', 'Merk berhasil diperbarui.');
    }
 
    /**
     * Hapus merk.
     */
    public function destroy(string $mrk)
    {
        $merk = MRK::findOrFail($mrk);
 
        try {
            $merk->delete();
        } catch (QueryException $e) {
            // Biasanya karena merk masih dipakai tabel lain (foreign key)
            return redirect()
                ->route('inventory.merk')
                ->with('error', 'Merk tidak dapat dihapus karena masih digunakan pada data lain.');
        }
 
        return redirect()
            ->route('inventory.merk')
            ->with('success', 'Merk berhasil dihapus.');
    }
}
