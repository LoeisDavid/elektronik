<?php

namespace App\Http\Controllers;

use App\Models\CST;
use App\Models\DS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CSTController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = CST::query();

            if ($request->filled('keyword')) {
                $keyword = trim((string) $request->keyword);
                $query->where(function ($q) use ($keyword) {
                    $q->where('DES', 'LIKE', "%{$keyword}%")
                        ->orWhere('CST', 'LIKE', "%{$keyword}%")
                        ->orWhere('TLP', 'LIKE', "%{$keyword}%")
                        ->orWhere('AL', 'LIKE', "%{$keyword}%")
                        ->orWhereExists(function ($sub) use ($keyword) {
                            $sub->select(DB::raw(1))
                                ->from('ds')
                                ->whereColumn('ds.DS', 'cst.DS')
                                ->where('ds.DES', 'LIKE', "%{$keyword}%");
                        });
                });
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
            $desaList = DS::orderBy('DES', 'asc')->get(['DS', 'DES']);

            return view('POS.customer', compact('data', 'desaList'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data customer');
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'DES' => 'required|string|max:150|unique:cst,DES',
            'TLP' => 'required|numeric|max_digits:20|unique:cst,TLP',
            'AL'  => 'required|string|max:100',
            'DS'  => 'required|string|max:15|exists:ds,DS',
        ], [
            'DES.required'   => 'Nama Customer wajib diisi.',
            'DES.string'     => 'Nama Customer harus berupa teks.',
            'DES.max'        => 'Nama Customer maksimal 150 karakter.',
            'DES.unique'     => 'Nama Customer sudah terdaftar.',
            'TLP.required'   => 'Nomor Telepon wajib diisi.',
            'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
            'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
            'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
            'AL.required'    => 'Alamat wajib diisi.',
            'AL.string'      => 'Alamat harus berupa teks.',
            'AL.max'         => 'Alamat maksimal 100 karakter.',
            'DS.required'    => 'Desa wajib dipilih.',
            'DS.exists'      => 'Desa yang dipilih tidak valid.',
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
            'AL'  => 'required|string|max:100',
            'DS'  => 'required|string|max:15|exists:ds,DS',
        ], [
            'DES.required'   => 'Nama Customer wajib diisi.',
            'DES.string'     => 'Nama Customer harus berupa teks.',
            'DES.max'        => 'Nama Customer maksimal 150 karakter.',
            'DES.unique'     => 'Nama Customer sudah terdaftar.',
            'TLP.required'   => 'Nomor Telepon wajib diisi.',
            'TLP.numeric'    => 'Nomor Telepon harus berupa angka.',
            'TLP.max_digits' => 'Nomor Telepon maksimal 20 digit.',
            'TLP.unique'     => 'Nomor Telepon sudah digunakan.',
            'AL.required'    => 'Alamat wajib diisi.',
            'AL.string'      => 'Alamat harus berupa teks.',
            'AL.max'         => 'Alamat maksimal 100 karakter.',
            'DS.required'    => 'Desa wajib dipilih.',
            'DS.exists'      => 'Desa yang dipilih tidak valid.',
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