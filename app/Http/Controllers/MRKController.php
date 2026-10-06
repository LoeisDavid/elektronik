<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MRK;
use Illuminate\Database\QueryException;

class MRKController extends Controller
{
    // halaman index untuk menampilkan semua data merk
    public function index(Request $request)
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data merk.');
        }
    }
 
    /**
     * Simpan merk baru.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'DES' => 'required|string|max:45',
            ]);

            $validated['MRK'] = $this->generateCode($validated['DES'], 'MRK');

            MRK::create($validated);
     
            return redirect()
                ->route('inventory.merk')
                ->with('success', 'Merk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan merk.');
        }
    }
 
    /**
     * Perbarui nama merk (kode merk tidak bisa diubah).
     */
    public function update(Request $request, string $mrk)
    {
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui merk.');
        }
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