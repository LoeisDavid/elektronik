<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where('USERNAME', 'LIKE', "%{$keyword}%")
                  ->orWhere('DES', 'LIKE', "%{$keyword}%")
                  ->orWhere('AL', 'LIKE', "%{$keyword}%");
        }

        if ($request->filled('sort')) {
            $query->orderBy('USERNAME', $request->sort);
        } else {
            $query->orderBy('USERNAME', 'desc');
        }

        $data = $query->paginate(10);

        return view('POS.user', compact('data'));
    }

    public function store(Request $request)
    {
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

        $customer = User::where('USERNAME', $decodedId)->firstOrFail();
        $customer->update($request->all());
        return redirect()->back()->with('success', 'Data berhasil diupdate');
    }

    public function destroy($id)
    {
        $customer = User::where('USERNAME', $id)->firstOrFail();
        $customer->delete();
        return redirect()->back()->with('success', 'Data berhasil dihapus');
    }
}
