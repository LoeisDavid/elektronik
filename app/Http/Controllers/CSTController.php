<?php

namespace App\Http\Controllers;

use App\Models\CST;
use Illuminate\Http\Request;

class CSTController extends Controller
{
    public function index(Request $request)
    {
        $query = CST::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('DES', 'LIKE', "%{$keyword}%")
                  ->orWhere('CST', 'LIKE', "%{$keyword}%")
                  ->orWhere('TLP', 'LIKE', "%{$keyword}%");
        }

        if ($request->filled('sort')) {
            $query->orderBy('DES', $request->sort);
        } else {
            $query->orderBy('DES', 'asc');
        }

        $data = $query->paginate(10);

        return view('POS.customer', compact('data'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'CST' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
            'TLP' => 'required|string|max:20',
        ]);

        CST::create($request->all());
        return redirect()->back()->with('success', 'Data berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        // Decode parameter ID untuk mengantisipasi karakter khusus
        $decodedId = urldecode($id);

        $request->validate([
            'CST' => 'required|string|max:10',
            'DES' => 'required|string|max:45',
            'TLP' => 'required|string|max:20',
        ]);

        $customer = CST::where('CST', $decodedId)->firstOrFail();
        $customer->update($request->all());
        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $customer = CST::where('CST', $id)->firstOrFail();
        $customer->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}