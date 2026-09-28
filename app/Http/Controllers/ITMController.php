<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ITM;
use App\Models\KTG;
use App\Models\MRK;
use App\Models\SPP;

class ITMController extends Controller
{
     // halaman index untuk menampilkan semua data item
    public function index(Request $request)
    {
        $query = ITM::query();

        if ($request->filled('keyword')) {
            $search = $request->keyword;
            $query->where('DES', 'LIKE', "%{$search}%")
                  ->orWhere('ITM', 'LIKE', "%{$search}%")
                  ->orWhere('KTG', 'LIKE', "%{$search}%")
                  ->orWhere('MRK', 'LIKE', "%{$search}%")
                  ->orWhere('SPP', 'LIKE', "%{$search}%")
                  ->orWhere('HRG', 'LIKE', "%{$search}%")
                  ->orWhere('STOK', 'LIKE', "%{$search}%");
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
        $MRK = MRK::all();
        $KTG = KTG::all();
        $SPP = SPP::all();

        return view('Inventory.item', compact('data', 'MRK', 'KTG', 'SPP'));
    }

    // halaman create untuk menampilkan form tambah data item
    public function create()
    {
        return view('Inventory.item.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ITM' => 'required|string|max:10',
            'DES' => 'required|string|max:150',
            'STOK' => 'required|integer',
            'HRG' => 'required|decimal:2',
            'KTG' => 'required|string|max:10',
            'MRK' => 'required|string|max:10',
            'SPP' => 'required|string|max:10',
        ]);

        ITM::create($validated);

        return redirect()->route('Inventory.item.index')->with('success', 'Data item berhasil ditambahkan.');
    }

    // halaman edit untuk menampilkan form edit data item
    public function edit($itm)
    {
        $itm = ITM::findOrFail($itm);
        return view('Inventory.item.edit', compact('itm'));
    }

    public function update(Request $request, $itm)
    {
        $validated = $request->validate([
            'ITM' => 'required|string|max:10',
            'DES' => 'required|string|max:150',
            'STOK' => 'required|integer',
            'HRG' => 'required|decimal:2',
            'KTG' => 'required|string|max:10',
            'MRK' => 'required|string|max:10',
            'SPP' => 'required|string|max:10',
        ]);

        $itm = ITM::findOrFail($itm);
        $itm->update($validated);

        return redirect()->route('Inventory.item.index')->with('success', 'Data item berhasil diperbarui.');
    }

    public function show($itm)
    {
        $itm = ITM::findOrFail($itm);
        return view('Inventory.detailItem', compact('itm'));
    }

    public function destroy($itm)
    {
        $itm = ITM::findOrFail($itm);
        $itm->delete();

        return redirect()->route('Inventory.item.index')->with('success', 'Data item berhasil dihapus.');
    }
}
