<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

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
        $validated = $request->validate([
            'USERNAME'     => ['required', 'string', 'max:20', 'unique:users,USERNAME'],
            'PASS'         => ['required', 'string', 'max:20'],
            'AL'           => ['nullable', 'string', 'max:100'],
            'HP'           => ['required', 'string', 'max:25', 'unique:users,HP'],
            'DES'          => ['required', 'string', 'max:100', 'unique:users,DES'],
            'ROLE'         => ['required', 'integer', 'in:1,2'],
        ], [
            'USERNAME.required'     => 'Username wajib diisi.',
            'USERNAME.string'       => 'Username harus berupa teks.',
            'USERNAME.max'          => 'Username maksimal 20 karakter.',
            'USERNAME.unique'       => 'Username sudah digunakan.',
            'PASS.required'         => 'Password wajib diisi.',
            'PASS.string'           => 'Password harus berupa teks.',
            'PASS.max'              => 'Password maksimal 20 karakter.',
            'AL.string'             => 'Alamat harus berupa teks.',
            'AL.max'                => 'Alamat maksimal 100 karakter.',
            'HP.required'           => 'Nomor Handphone wajib diisi.',
            'HP.string'             => 'Nomor Handphone harus berupa teks.',
            'HP.max'                => 'Nomor Handphone maksimal 25 karakter.',
            'HP.unique'             => 'Nomor Handphone sudah digunakan.',
            'DES.required'          => 'Nama Lengkap wajib diisi.',
            'DES.string'            => 'Nama Lengkap harus berupa teks.',
            'DES.max'               => 'Nama Lengkap maksimal 100 karakter.',
            'DES.unique'            => 'Nama Lengkap sudah terdaftar.',
            'ROLE.required'         => 'Role wajib diisi.',
            'ROLE.integer'          => 'Role harus berupa angka.',
            'ROLE.in'               => 'Role harus dipilih (Admin atau Kasir).',
        ]);

        try {
            User::create($validated);
            return redirect()->route('POS.user')->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Gagal tambah user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan ketika menambahkan data user');
        }
    }

    public function update(Request $request, $id)
    {
        // Decode parameter ID untuk mengantisipasi karakter khusus
        $decodedId = urldecode($id);
        $user = User::where('USERNAME', $decodedId)->firstOrFail();

        // Pastikan USERNAME terisi sesuai user yang diedit jika tidak terkirim dari readonly field
        if (!$request->filled('USERNAME')) {
            $request->merge(['USERNAME' => $decodedId]);
        }

        $rules = [
            'USERNAME' => ['required', 'string', 'max:20', Rule::unique('users', 'USERNAME')->ignore($user->USERNAME, 'USERNAME')],
            'AL'       => ['nullable', 'string', 'max:100'],
            'HP'       => ['required', 'string', 'max:25', Rule::unique('users', 'HP')->ignore($user->USERNAME, 'USERNAME')],
            'DES'      => ['required', 'string', 'max:100', Rule::unique('users', 'DES')->ignore($user->USERNAME, 'USERNAME')],
            'ROLE'     => ['required', 'integer', 'in:1,2'],
        ];

        if ($request->filled('PASS')) {
            $rules['PASS'] = ['string', 'max:20'];
        }

        $validated = $request->validateWithBag('editUser', $rules, [
            'USERNAME.required'     => 'Username wajib diisi.',
            'USERNAME.string'       => 'Username harus berupa teks.',
            'USERNAME.max'          => 'Username maksimal 20 karakter.',
            'USERNAME.unique'       => 'Username sudah digunakan.',
            'PASS.string'           => 'Password harus berupa teks.',
            'PASS.max'              => 'Password maksimal 20 karakter.',
            'AL.string'             => 'Alamat harus berupa teks.',
            'AL.max'                => 'Alamat maksimal 100 karakter.',
            'HP.required'           => 'Nomor Handphone wajib diisi.',
            'HP.string'             => 'Nomor Handphone harus berupa teks.',
            'HP.max'                => 'Nomor Handphone maksimal 25 karakter.',
            'HP.unique'             => 'Nomor Handphone sudah digunakan.',
            'DES.required'          => 'Nama Lengkap wajib diisi.',
            'DES.string'            => 'Nama Lengkap harus berupa teks.',
            'DES.max'               => 'Nama Lengkap maksimal 100 karakter.',
            'DES.unique'            => 'Nama Lengkap sudah terdaftar.',
            'ROLE.required'         => 'Role wajib diisi.',
            'ROLE.integer'          => 'Role harus berupa angka.',
            'ROLE.in'               => 'Role harus dipilih (Admin atau Kasir).',
        ]);

        if (empty($validated['PASS'])) {
            unset($validated['PASS']);
        }

        try {
            $user->update($validated);
            return redirect()->route('POS.user')->with('success', 'User berhasil diubah.');
        } catch (\Exception $e) {
            Log::error('Gagal update user: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan ketika mengupdate data user');
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
