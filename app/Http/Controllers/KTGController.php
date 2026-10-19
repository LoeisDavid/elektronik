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
        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data kategori.');
        }
    }
 
    /**
     * Simpan kategori baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:45|unique:ktg,DES',
        ], [
            'DES.required' => 'Nama kategori wajib diisi.',
            'DES.string'   => 'Nama kategori harus berupa teks.',
            'DES.max'      => 'Nama kategori maksimal 45 karakter.',
            'DES.unique'   => 'Nama kategori sudah terdaftar.',
        ]);

        try {
            $validated['KTG'] = $this->generateCode($validated['DES'], 'KTG');
     
            KTG::create($validated);

            return redirect()->route('inventory.kategori')->with('success', 'Data kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan kategori.');
        }
    }
 
    /**
     * Perbarui nama kategori (kode kategori tidak bisa diubah).
     */
    public function update(Request $request, string $ktg)
    {
        $decodedKtg = urldecode($ktg);
        $kategori = KTG::where('KTG', $decodedKtg)->firstOrFail();

        $validated = $request->validateWithBag('editKategori', [
            'KTG' => 'required|string|max:10',
            'DES' => 'required|string|max:45|unique:ktg,DES,' . $kategori->KTG . ',KTG',
        ], [
            'DES.required' => 'Nama kategori wajib diisi.',
            'DES.string'   => 'Nama kategori harus berupa teks.',
            'DES.max'      => 'Nama kategori maksimal 45 karakter.',
            'DES.unique'   => 'Nama kategori sudah terdaftar.',
        ]);

        try {
            $kategori->update($validated);
     
            return redirect()
                ->route('inventory.kategori')->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui kategori.');
        }
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