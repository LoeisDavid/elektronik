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
        $validated = $request->validate([
            'DES' => 'required|string|max:150|unique:cst,DES',
            'TLP' => 'required|numeric|max_digits:20|unique:cst,TLP',
        ], [
            'DES.required'   => 'Nama Customer wajib diisi.',
            'DES.string'     => 'Nama Customer harus berupa teks.',
            'DES.max'        => 'Nama Customer maksimal 150 karakter.',
            'DES.unique'     => 'Nama Customer sudah terdaftar.',
            'TLP.required'   => 'Nomor Telepon wajib diisi.',
            'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
            'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
            'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
        ]);

        try {
            $validated['CST'] = $this->generateCode($validated['DES'], 'CST');

            CST::create($validated);
            return redirect()->route('POS.customer')->with('success', 'Customer berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data customer');
        }
    }

    public function update(Request $request, $id)
    {
        // Decode parameter ID untuk mengantisipasi karakter khusus
        $decodedId = urldecode($id);

        $customer = CST::where('CST', $decodedId)->firstOrFail();

        $validated = $request->validateWithBag('editCustomer', [
            'CST' => 'required|string|max:10|unique:cst,CST,' . $customer->CST . ',CST',
            'DES' => 'required|string|max:150|unique:cst,DES,' . $customer->CST . ',CST',
            'TLP' => 'required|numeric|max_digits:20|unique:cst,TLP,' . $customer->CST . ',CST',
        ], [
            'DES.required'   => 'Nama Customer wajib diisi.',
            'DES.string'     => 'Nama Customer harus berupa teks.',
            'DES.max'        => 'Nama Customer maksimal 150 karakter.',
            'DES.unique'     => 'Nama Customer sudah terdaftar.',
            'TLP.required'   => 'Nomor Telepon wajib diisi.',
            'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
            'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
            'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
        ]);

        try {
            $customer->update($validated);
            return redirect()->route('POS.customer')->with('success', 'Customer berhasil diubah.');
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