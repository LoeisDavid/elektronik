<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPP;

class SPPController extends Controller
{
     // halaman index untuk menampilkan semua data supplier
    public function index(Request $request)
    {
        try {
            $query = SPP::query();

            if ($request->filled('keyword')) {
                $search = $request->keyword;
                $query->where('DES', 'LIKE', "%{$search}%")
                      ->orWhere('SPP', 'LIKE', "%{$search}%")
                      ->orWhere('TLP', 'LIKE', "%{$search}%");
            }

            $sort = $request->sort;

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

            $data = $query->paginate(10)->withQueryString();

            return view('Inventory.supplier', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data supplier.');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:150|unique:spp,DES',
            'TLP' => 'required|numeric|max_digits:20|unique:spp,TLP',
        ], [
            'DES.required'   => 'Nama Supplier wajib diisi.',
            'DES.string'     => 'Nama Supplier harus berupa teks.',
            'DES.max'        => 'Nama Supplier maksimal 150 karakter.',
            'DES.unique'     => 'Nama Supplier sudah terdaftar.',
            'TLP.required'   => 'Nomor Telepon wajib diisi.',
            'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
            'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
            'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
        ]);

        try {
            $validated['SPP'] = $this->generateCode($validated['DES'], 'SPP');

            SPP::create($validated);

            return redirect()->route('inventory.supplier')->with('success', 'Data Supplier berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan supplier.');
        }
    }

    // halaman edit untuk menampilkan form edit data supplier
    public function edit($spp)
    {
        try {
            $spp = SPP::findOrFail($spp);
            return view('supplier.edit', compact('spp'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data supplier.');
        }
    }

    public function update(Request $request, $spp)
    {
        $decodedSpp = urldecode($spp);
        $supplier = SPP::where('SPP', $decodedSpp)->firstOrFail();

        $validated = $request->validateWithBag('editSupplier', [
            'SPP' => 'required|string|max:10|unique:spp,SPP,' . $supplier->SPP . ',SPP',
            'DES' => 'required|string|max:150|unique:spp,DES,' . $supplier->SPP . ',SPP',
            'TLP' => 'required|numeric|max_digits:20|unique:spp,TLP,' . $supplier->SPP . ',SPP',
        ], [
            'DES.required'   => 'Nama Supplier wajib diisi.',
            'DES.string'     => 'Nama Supplier harus berupa teks.',
            'DES.max'        => 'Nama Supplier maksimal 150 karakter.',
            'DES.unique'     => 'Nama Supplier sudah terdaftar.',
            'TLP.required'   => 'Nomor Telepon wajib diisi.',
            'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
            'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
            'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
        ]);

        try {
            $supplier->update($validated);

            return redirect()->route('inventory.supplier')->with('success', 'Data supplier berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui supplier.');
        }
    }

    public function destroy($spp)
    {
        try {
            $spp = SPP::findOrFail($spp);
            $spp->delete();

            return redirect()->back()->with('success', 'Data supplier berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus supplier.');
        }
    }
}