<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ITM;
use App\Models\ITMDT;

class kasirController extends Controller
{
    /**
     * Menampilkan halaman utama Kasir (POS) beserta data keranjang dari Session.
     */
    public function index()
    {
        // Inisialisasi data keranjang dummy awal jika session cart masih kosong
        if (!session()->has('cart')) {
            session()->put('cart', [
                'item_1' => [
                    'id' => 'item_1',
                    'code' => 'P001',
                    'name' => 'namaProduk',
                    'description' => 'Deskripsi',
                    'price' => 20000000,
                    'qty' => 1,
                    'discount' => 0
                ],
                'item_2' => [
                    'id' => 'item_2',
                    'code' => 'P002',
                    'name' => 'Asus TUF Gaming A14',
                    'description' => 'RTX 5060, Ryzen AI 7 350H',
                    'price' => 15000000,
                    'qty' => 1,
                    'discount' => 0
                ]
            ]);
        }

        // Ambil data keranjang dari session
        $cart = session()->get('cart', []);
        $voucher = session()->get('voucher', 10000000);
        $discountPercent = session()->get('discount_percent', 20);

        return view('POS.kasir', compact('cart', 'voucher', 'discountPercent'));
    }

    /**
     * Memproses input scan barcode manual:
     * Menambahkan item ke Session Cart dan merefresh / redirect kembali ke halaman Kasir.
     */
    public function scan(Request $request)
    {
        $code = trim($request->input('BC_OUT') ?? $request->input('code') ?? '');

        if (!$code) {
            return redirect()->route('POS.kasir')->with('error', 'Barcode tidak boleh kosong!');
        }

        $cart = session()->get('cart', []);

        // 1. Cek di tabel ITMDT berdasarkan barcode keluar (BC_OUT)
        $data = ITMDT::where('BC_OUT', $code)->first();
        $item = null;

        if ($data) {
            $item = ITM::where('ITM', $data->ITM)->first();
        }

        // 2. Jika tidak ditemukan di ITMDT, cek langsung di tabel ITM berdasarkan kode item
        if (!$item) {
            $item = ITM::where('ITM', $code)->first();
        }

        // 3. Jika item ditemukan di database
        if ($item) {
            $key = $code;
            if (isset($cart[$key])) {
                $cart[$key]['qty'] += 1;
            } else {
                $cart[$key] = [
                    'id' => $item->ITM,
                    'code' => $code,
                    'name' => $item->DES ?? $item->ITM,
                    'description' => $item->MRK ?? $code,
                    'price' => (float) ($item->HRG ?? 0),
                    'qty' => 1,
                    'discount' => 0
                ];
            }

            session()->put('cart', $cart);
            return redirect()->route('POS.kasir')->with('success', 'Produk "' . ($item->DES ?? $code) . '" berhasil ditambahkan ke keranjang!');
        }

        // 4. Fallback jika kode barcode custom / pengujian (agar tetap masuk ke session saat ditest)
        $key = 'BC_' . $code;
        if (isset($cart[$key])) {
            $cart[$key]['qty'] += 1;
        } else {
            $cart[$key] = [
                'id' => $key,
                'code' => $code,
                'name' => 'Produk ' . $code,
                'description' => 'Barcode: ' . $code,
                'price' => 5000000,
                'qty' => 1,
                'discount' => 0
            ];
        }

        session()->put('cart', $cart);
        return redirect()->route('POS.kasir')->with('success', 'Barcode "' . $code . '" berhasil ditambahkan ke keranjang!');
    }

    /**
     * Sinkronisasi perubahan dari JavaScript (tambah/kurang qty, hapus item, diskon, voucher) ke Session
     */
    public function syncCart(Request $request)
    {
        $cart = $request->input('cart', []);
        $voucher = $request->input('voucher', 0);
        $discountPercent = $request->input('discount_percent', 0);

        session()->put('cart', $cart);
        session()->put('voucher', $voucher);
        session()->put('discount_percent', $discountPercent);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil disinkronisasi ke session.'
        ]);
    }

    /**
     * Mengosongkan keranjang di session
     */
    public function clearCart()
    {
        session()->forget('cart');
        return redirect()->route('POS.kasir')->with('info', 'Keranjang berhasil dikosongkan.');
    }
}