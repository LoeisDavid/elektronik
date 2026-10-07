<?php

namespace App\Http\Controllers;

use App\Models\CST;
use Illuminate\Http\Request;

class CSTController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = CST::query();

            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where('DES', 'LIKE', "%{$keyword}%")
                    ->orWhere('CST', 'LIKE', "%{$keyword}%")
                    ->orWhere('TLP', 'LIKE', "%{$keyword}%");
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

            return view('POS.customer', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data customer');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'DES' => 'required|string|max:45',
                'TLP' => 'required|numeric|max_digits:20',
            ]);

            $validated['CST'] = $this->generateCode($validated['DES'], 'CST');

            CST::create($validated);
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data customer');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Decode parameter ID untuk mengantisipasi karakter khusus
            $decodedId = urldecode($id);

            $request->validate([
                'CST' => 'required|string|max:10',
                'DES' => 'required|string|max:45',
                'TLP' => 'required|numeric|max_digits:20',
            ]);

            $customer = CST::where('CST', $decodedId)->firstOrFail();
            $customer->update($request->all());
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika mengupdate data customer');
        }
    }

    public function destroy($id)
    {
        try {
            $customer = CST::where('CST', $id)->firstOrFail();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Customer tidak ditemukan');
        }

        try {
            $customer->delete();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus data customer');
        }
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}