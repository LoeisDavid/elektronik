<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SPP;

class SPPController extends Controller
{
     // halaman index untuk menampilkan semua data supplier
    public function index(Request $request)
    {
        $query = SPP::query();

        if ($request->filled('keyword')) {
            $search = $request->keyword;
            $query->where('DES', 'LIKE', "%{$search}%")
                  ->orWhere('SPP', 'LIKE', "%{$search}%");
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
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:150',
            'TLP' => 'required|string|max:20',
        ]);

        $data['SPP'] = $this->generateCode($validated['DES'], 'SPP');

        SPP::create($validated);

        return redirect()->back()->with('success', 'Data Supplier berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data supplier
    public function edit($spp)
    {
        $spp = SPP::findOrFail($spp);
        return view('supplier.edit', compact('spp'));
    }

    public function update(Request $request, $spp)
    {
        $validated = $request->validate([
            'SPP' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
            'TLP' => 'required|string|max:20',
        ]);

        $spp = SPP::findOrFail($spp);
        $spp->update($validated);

        return redirect()->back()->with('success', 'Data supplier berhasil diperbarui.');
    }

    public function destroy($spp)
    {
        $spp = SPP::findOrFail($spp);
        $spp->delete();

        return redirect()->back()->with('success', 'Data supplier berhasil dihapus.');
    }
}
