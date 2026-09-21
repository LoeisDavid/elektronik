<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\ACR;
use App\Models\STS;
use App\Models\CST;
use App\Models\SLS;
use Illuminate\Support\Facades\DB;

class kasirController extends Controller
{
    /**
     * Menampilkan halaman utama Kasir (POS) beserta data keranjang dari Session.
     */
    public function index()
    {
        // Ambil data keranjang, voucher, dan diskon dari session
        $cart = session()->get('cart', []);
        $voucher = session()->get('voucher', 0);
        $discountPercent = session()->get('discount_percent', 0);

        return view('POS.kasir', compact('cart', 'voucher', 'discountPercent'));
    }

    /**
     * Memproses input scan barcode manual:
     * Setiap fisik barang memiliki barcode unik (BC_OUT). Meskipun tipe barang sama,
     * jika BC_OUT berbeda maka dianggap sebagai barang yang berbeda di dalam keranjang.
     */
    public function scan(Request $request)
    {
        $code = trim($request->input('BC_OUT') ?? $request->input('code') ?? '');

        if (!$code) {
            return redirect()->route('POS.kasir')->with('error', 'Barcode tidak boleh kosong!');
        }

        $cart = session()->get('cart', []);

        // Cek apakah barcode BC_OUT ini sudah ada di keranjang
        if (isset($cart[$code])) {
            return redirect()->route('POS.kasir')->with('warning', 'Barcode "' . $code . '" sudah ada di keranjang!');
        }

        // Cek apakah barcode sudah tercatat di tabel STS (sudah terjual)
        if (STS::where('BC_OUT', $code)->exists()) {
            return redirect()->route('POS.kasir')->with('error', 'Barcode "' . $code . '" sudah terjual dan tidak dapat digunakan lagi!');
        }

        // 1. Cek di tabel ITMDT berdasarkan barcode keluar (BC_OUT)
        $itmdt = ITMDT::where('BC_OUT', $code)->first();
        if (!$itmdt) {
            return redirect()->route('POS.kasir')->with('error', 'Barcode tidak ditemukan!');
        }
        $item = null;

        if ($itmdt) {
            $item = ITM::where('ITM', $itmdt->ITM)->first();
        } else {
            // 2. Jika bukan BC_OUT, cek apakah kode adalah kode produk ITM
            $item = ITM::where('ITM', $code)->first();
            if ($item) {
                // Cari BC_OUT dari ITMDT yang belum ada di keranjang
                $existingBcOuts = array_keys($cart);
                $availableItmdt = ITMDT::where('ITM', $item->ITM)
                    ->whereNotIn('BC_OUT', $existingBcOuts)
                    ->first();

                if ($availableItmdt) {
                    $code = $availableItmdt->BC_OUT;
                }
            }
        }

        // 3. Jika item ditemukan di database
        if ($item) {
            $cart[$code] = [
                'id' => $item->ITM,
                'code' => $code, // Barcode BC_OUT unik untuk unit barang ini
                'name' => $item->DES ?? $item->ITM,
                'description' => $item->mrk()?->DES ?? '',
                'price' => (float) ($item->HRG ?? 0),
                'qty' => 1,
                'discount' => 0
            ];

            session()->put('cart', $cart);
            return redirect()->route('POS.kasir')->with('success', 'Produk "' . ($item->DES ?? $code) . '" (Barcode: ' . $code . ') berhasil ditambahkan!');
        }

        // 4. Fallback jika kode barcode custom / dummy pengujian
        $cart[$code] = [
            'id' => 'ITM_' . substr($code, 0, 8),
            'code' => $code,
            'name' => 'Produk ' . $code,
            'description' => 'Merek: ' . $code,
            'price' => 5000000,
            'qty' => 1,
            'discount' => 0
        ];

        session()->put('cart', $cart);
        return redirect()->route('POS.kasir')->with('success', 'Barcode "' . $code . '" berhasil ditambahkan ke keranjang!');
    }

    /**
     * Memproses transaksi pembayaran dan menyimpan data ke tabel ACR dan STS
     */
    public function bayar(Request $request)
    {
        $cart = $request->input('cart', session()->get('cart', []));
        $voucherAmount = (float) ($request->input('voucher', session()->get('voucher', 0)));
        $discountPercent = (float) ($request->input('discount_percent', session()->get('discount_percent', 0)));
        $paymentMethod = $request->input('payment_method', 'tunai');
        $bankName = $request->input('bank_name', null);
        $nominalBayar = (float) ($request->input('nominal_bayar', 0));

        if (empty($cart) || !is_array($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang masih kosong!'
            ], 400);
        }

        // Hitung Subtotal dan Total Transaksi
        $subtotal = 0;
        foreach ($cart as $item) {
            $itemPrice = (float) ($item['price'] ?? 0);
            $itemDiscount = (float) ($item['discount'] ?? 0);
            $priceAfterDiscount = $itemPrice - ($itemPrice * ($itemDiscount / 100));
            $subtotal += $priceAfterDiscount;
        }

        $diskonTransaksiNominal = $subtotal * ($discountPercent / 100);
        $grandTotal = max(0, $subtotal - $voucherAmount - $diskonTransaksiNominal);

        try {
            DB::beginTransaction();

            // 1. Generate REF_NO untuk tabel ACR (INV0000001 - max 10 karakter)
            $lastAcr = ACR::where('REF_NO', 'like', 'INV%')->orderBy('REF_NO', 'desc')->first();
            $nextNumber = 1;
            if ($lastAcr) {
                $numOnly = (int) preg_replace('/\D/', '', $lastAcr->REF_NO);
                $nextNumber = $numOnly + 1;
            }
            $refNo = sprintf('INV%07d', $nextNumber);
            while (ACR::where('REF_NO', $refNo)->exists()) {
                $nextNumber++;
                $refNo = sprintf('INV%07d', $nextNumber);
            }

            // Dapatkan CST, SLS, USER default
            $cst = CST::first()?->CST ?? 'CST0000001';
            $sls = SLS::first()?->SLS ?? 'SLS0000001';
            $user = auth()->user()?->USERNAME ?? 'admin';

            // 2. Simpan Header Transaksi ke Tabel ACR
            $acr = ACR::create([
                'REF_NO' => $refNo,
                'DT'     => now()->toDateString(),
                'CST'    => substr($cst, 0, 10),
                'SLS'    => substr($sls, 0, 10),
                'BRT'    => $grandTotal,
                'POT_1'  => (int) $discountPercent,
                'POT_2'  => null,
                'POT_R'  => $voucherAmount > 0 ? $voucherAmount : null,
                'USER'   => substr($user, 0, 10),
            ]);

            // 3. Simpan Rincian Barang ke Tabel STS (Setiap unit barang memiliki BC_OUT berbeda)
            foreach ($cart as $key => $item) {
                $bcOut = substr($item['code'] ?? $key, 0, 20);
                $itmCode = substr($item['id'] ?? $bcOut, 0, 20);
                $hgj = (float) ($item['price'] ?? 0);
                $pot1 = (int) ($item['discount'] ?? 0);
                $brtItem = max(0, $hgj - ($hgj * ($pot1 / 100)));

                // Generate REC unik (3 karakter)
                do {
                    $rec = strtoupper(substr(bin2hex(random_bytes(2)), 0, 3));
                } while (STS::where('REC', $rec)->exists());

                STS::create([
                    'REC'    => $rec,
                    'REF_NO' => $refNo,
                    'BC_OUT' => $bcOut,
                    'ITM'    => $itmCode,
                    'HGJ'    => $hgj,
                    'ST'     => 'UNT',
                    'POT_1'  => $pot1 > 0 ? $pot1 : null,
                    'POT_2'  => null,
                    'POT_R'  => null,
                    'PPN'    => null,
                    'BRT'    => $brtItem,
                ]);
            }

            // Kosongkan keranjang setelah berhasil dibayar
            session()->forget(['cart', 'voucher', 'discount_percent']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil disimpan.',
                'ref_no'  => $refNo,
                'grand_total' => $grandTotal
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sinkronisasi perubahan dari JavaScript (hapus item, diskon, voucher) ke Session
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
        session()->forget(['cart', 'voucher', 'discount_percent']);
        return redirect()->route('POS.kasir')->with('info', 'Keranjang berhasil dikosongkan.');
    }
}