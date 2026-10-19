<?php

namespace App\Http\Controllers;

use App\Models\DS;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class DSController extends Controller
{
    /**
     * Halaman index untuk menampilkan data desa.
     */
    public function index(Request $request)
    {
        try {
            $query = DS::query();

            if ($request->filled('keyword')) {
                $keyword = trim((string) $request->keyword);
                $query->where(function ($q) use ($keyword) {
                    $q->where('DES', 'LIKE', "%{$keyword}%")
                      ->orWhere('DS', 'LIKE', "%{$keyword}%");
                });
            }

            $sort = $request->query('sort', 'a-z');

            if ($sort === 'a-z') {
                $query->orderBy('DES', 'asc');
            } elseif ($sort === 'z-a') {
                $query->orderBy('DES', 'desc');
            } elseif ($sort === 'terbanyak') {
                // Placeholder sort untuk terbanyak (dapat disesuaikan jika relasi transaksi sudah tersedia)
                $query->orderBy('created_at', 'desc');
            } elseif ($sort === 'terdikit') {
                // Placeholder sort untuk terdikit
                $query->orderBy('created_at', 'asc');
            } else {
                $query->orderBy('DES', 'asc');
            }

            $data = $query->paginate(10)->withQueryString();

            return view('POS.desa', compact('data', 'sort'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data desa.');
        }
    }

    /**
     * Simpan desa baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:50|unique:ds,DES',
        ], [
            'DES.required' => 'Nama Desa wajib diisi.',
            'DES.string'   => 'Nama Desa harus berupa teks.',
            'DES.max'      => 'Nama Desa maksimal 50 karakter.',
            'DES.unique'   => 'Nama Desa sudah terdaftar.',
        ]);

        try {
            $validated['DS'] = $this->generateCode($validated['DES'], 'ds');

            DS::create($validated);

            return redirect()->route('POS.desa')->with('success', 'Data desa berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data desa.');
        }
    }

    /**
     * Perbarui nama desa.
     */
    public function update(Request $request, string $id)
    {
        $decodedId = urldecode($id);
        $desa = DS::where('DS', $decodedId)->firstOrFail();

        $validated = $request->validateWithBag('editDesa', [
            'DES' => 'required|string|max:50|unique:ds,DES,' . $desa->DS . ',DS',
        ], [
            'DES.required' => 'Nama Desa wajib diisi.',
            'DES.string'   => 'Nama Desa harus berupa teks.',
            'DES.max'      => 'Nama Desa maksimal 50 karakter.',
            'DES.unique'   => 'Nama Desa sudah terdaftar.',
        ]);

        try {
            $desa->update([
                'DES' => $validated['DES'],
            ]);

            return redirect()->route('POS.desa')->with('success', 'Data desa berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui data desa.');
        }
    }

    /**
     * Hapus desa.
     */
    public function destroy(string $id)
    {
        $decodedId = urldecode($id);
        $desa = DS::where('DS', $decodedId)->firstOrFail();

        try {
            $desa->delete();
            return redirect()->route('POS.desa')->with('success', 'Data desa berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('POS.desa')->with('error', 'Data desa tidak dapat dihapus karena masih digunakan.');
        } catch (\Exception $e) {
            return redirect()->route('POS.desa')->with('error', 'Terjadi kesalahan ketika menghapus data desa.');
        }
    }
}
