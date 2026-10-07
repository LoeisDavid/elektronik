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
        try {
            $validated = $request->validate([
                'DES' => 'required|string|max:150',
                'TLP' => 'required|numeric|max_digits:20',
            ]);

            $validated['SPP'] = $this->generateCode($validated['DES'], 'SPP');

            SPP::create($validated);

            return redirect()->back()->with('success', 'Data Supplier berhasil ditambahkan.');
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
        try {
            $validated = $request->validate([
                'SPP' => 'required|string|max:10',
                'DES' => 'required|string|max:45',
                'TLP' => 'required|numeric|max_digits:20',
            ]);

            $spp = SPP::findOrFail($spp);
            $spp->update($validated);

            return redirect()->back()->with('success', 'Data supplier berhasil diperbarui.');
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