<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = User::query();
            $sort = $request->sort;

            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where('USERNAME', 'LIKE', "%{$keyword}%")
                    ->orWhere('DES', 'LIKE', "%{$keyword}%")
                    ->orWhere('AL', 'LIKE', "%{$keyword}%")
                    ->orWhere('HP', 'LIKE', "%{$keyword}%");
            }

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

            return view('POS.user', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data user');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'USERNAME' => 'required|string|max:20',
                'PASS' => 'required|string|max:20',
                'AL' => 'string|max:100',
                'HP' => 'required|string|max:25',
                'DES' => 'required|string|max:100',
                'ROLE' => 'required|integer',
            ]);

            User::create($request->all());
            return redirect()->back()->with('success', 'Data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menambahkan data user');
        }
    }

    public function update(Request $request, $id)
    {
        // Decode parameter ID untuk mengantisipasi karakter khusus
        try {
            $decodedId = urldecode($id);

            $validated = $request->validate([
                'AL' => 'string|max:100',
                'HP' => 'required|string|max:25',
                'DES' => 'required|string|max:100',
                'ROLE' => 'required|integer',
            ]);

            $customer = User::where('USERNAME', $decodedId)->firstOrFail();
            $customer->update($validated);
            return redirect()->back()->with('success', 'Data berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika mengupdate data user');
        }
    }

    public function destroy($id)
    {
        try {
            $customer = User::where('USERNAME', $id)->firstOrFail();
            $customer->delete();
            return redirect()->back()->with('success', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menghapus data user');
        }
    }
}
