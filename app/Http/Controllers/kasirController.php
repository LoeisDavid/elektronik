<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\ACR;
use App\Models\STS;
use App\Models\CST;
use App\Models\DS;
use App\Models\SLS;
use App\Models\KAS;
use Illuminate\Support\Facades\DB;

class kasirController extends Controller
{
    /**
     * Shift kasir yang sedang aktif (TGLK masih kosong), yang terbaru.
     * Tidak memakai filter tanggal karena tiap kas berdiri sendiri.
     */
    private function kasAktif(): ?KAS
    {
        return KAS::whereNull('TGLK')
            ->orderBy('TGLM', 'desc')
            ->first();
    }

    /**
     * Menampilkan halaman utama Kasir (POS) beserta data keranjang dari Session.
     */
    public function index()
    {
        // Ambil data keranjang, voucher, diskon, dan PPN dari session
        $cart = session()->get('cart', []);
        $voucher = session()->get('voucher', 0);
        $discountPercent = session()->get('discount_percent', 0);
        $ppn = session()->get('ppn', null);

        // Status kasir ditentukan dari database (shift aktif)
        $activeKas = $this->kasAktif();
        $isKasirOpen = $activeKas !== null;

        // Sinkronkan session dengan kondisi database
        if ($activeKas) {
            session()->put('kas_open', true);
            session()->put('kas_opening', (int) $activeKas->AWLT);
        } else {
            session()->forget(['kas_open', 'kas_opening', 'catatan_opening']);
        }

        // Ambil daftar customer dan sales untuk autocomplete / pencarian
        $customers = CST::select('CST', 'DES', 'TLP', 'DS')->orderBy('DES', 'asc')->get();
        $desaList = CST::whereNull('DS')->select('CST', 'DES')->orderBy('DES', 'asc')->get();
        $salesList = SLS::select('SLS', 'DES', 'HP')->get();

        return view('POS.kasir', compact('cart', 'voucher', 'discountPercent', 'ppn', 'isKasirOpen', 'activeKas', 'customers', 'desaList', 'salesList'));
    }

    /**
     * Memproses Buka Kasir (Kas Opening dan Catatan Opening)
     */
    public function bukaKasir(Request $request)
    {
        $rawOpening = (string) ($request->input('kas_opening') ?? 0);
        $nominalOpening = (int) (preg_replace('/[^\d]/', '', $rawOpening) ?: 0);
        $today = now()->toDateString();
        $user = auth()->user()?->USERNAME ?? 'admin';

        try {
            // Jika sudah ada shift aktif, jangan buat baris baru
            $kas = $this->kasAktif();

            if (!$kas) {
                $cd = 'CD' . sprintf('%08d', KAS::count() + 1);
                while (KAS::where('CD', $cd)->exists()) {
                    $cd = 'CD' . sprintf('%08d', rand(10000, 99999999));
                }

                $kas = KAS::create([
                    'CD'    => $cd,
                    'TGL'   => $today,
                    'USER'  => substr($user, 0, 10),
                    'AWLT'  => $nominalOpening,
                    'AWLNT' => 0,
                    'DBT'   => 0,
                    'DBNT'  => 0,
                    'CRT'   => 0,
                    'CRNT'  => 0,
                    'AKHT'  => $nominalOpening,
                    'AKHNT' => 0,
                    'TGLM'  => now(),
                    'TGLK'  => null,
                ]);
            }

            session()->put('kas_open', true);
            session()->put('kas_opening', $nominalOpening);

            return response()->json([
                'success' => true,
                'message' => 'Kasir berhasil dibuka.',
                'kas'     => $kas,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuka kasir: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mengambil status dan ringkasan kas aktif
     */
    public function kasStatus()
    {
        $kas = $this->kasAktif();

        if (!$kas) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kasir yang sedang dibuka.',
            ]);
        }

        $saldoAwal         = (int) ($kas->AWLT ?? 0);
        $penjualanTunai    = (int) ($kas->DBT ?? 0);
        $penjualanNonTunai = (int) ($kas->DBNT ?? 0);
        $mutasiKeluar      = (int) ($kas->CRT ?? 0);
        $saldoAkhir        = $saldoAwal + $penjualanTunai - $mutasiKeluar;

        return response()->json([
            'success' => true,
            'kas' => [
                'saldo_awal'          => $saldoAwal,
                'penjualan_tunai'     => $penjualanTunai,
                'penjualan_non_tunai' => $penjualanNonTunai,
                'mutasi_keluar'       => $mutasiKeluar,
                'saldo_akhir'         => $saldoAkhir,
                'total_kas'           => $saldoAkhir,
            ],
        ]);
    }

    /**
     * Memproses Tutup Kasir
     */
    public function tutupKasir(Request $request)
    {
        try {
            $activeKasList = KAS::whereNull('TGLK')->get();

            $nominalAkhirTunaiInput = $request->filled('nominal_akhir_tunai')
                ? (float) preg_replace('/[^0-9]/', '', (string) $request->input('nominal_akhir_tunai'))
                : null;

            foreach ($activeKasList as $kas) {
                $saldoAkhirTunai    = $nominalAkhirTunaiInput !== null
                    ? $nominalAkhirTunaiInput
                    : (($kas->AWLT ?? 0) + ($kas->DBT ?? 0) - ($kas->CRT ?? 0));
                $saldoAkhirNonTunai = ($kas->AWLNT ?? 0) + ($kas->DBNT ?? 0) - ($kas->CRNT ?? 0);

                $kas->update([
                    'TGLK'  => now(),
                    'AKHT'  => $saldoAkhirTunai,
                    'AKHNT' => $saldoAkhirNonTunai,
                ]);
            }

            // Hapus session kasir dan cart
            session()->forget(['kas_open', 'kas_opening', 'cart', 'voucher', 'discount_percent']);

            return response()->json([
                'success' => true,
                'message' => 'Kasir berhasil ditutup.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menutup kasir: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pencarian produk dari tabel ITM (berdasarkan Nama/DES, Harga/HRG, dan Kode/ITM).
     * Mengembalikan juga stok unit BC_OUT yang tersedia (ada di ITMDT dan belum terjual di STS).
     */
    public function searchItems(Request $request)
    {
        $q = trim($request->input('q', ''));
        if ($q === '') {
            return response()->json([
                'success' => true,
                'items'   => []
            ]);
        }

        // Cari di tabel ITM berdasarkan DES (nama), ITM (kode), atau HRG (harga)
        $items = ITM::where(function ($query) use ($q) {
            $query->where('DES', 'LIKE', "%{$q}%")
                  ->orWhere('ITM', 'LIKE', "%{$q}%")
                  ->orWhere('HRG', 'LIKE', "%{$q}%");
        })
        ->limit(20)
        ->get();

        if ($items->isEmpty()) {
            return response()->json([
                'success' => true,
                'items'   => []
            ]);
        }

        $itemCodes = $items->pluck('ITM')->toArray();

        // Cari unit BC_OUT dari ITMDT yang belum terjual (tidak ada di STS)
        $availableUnits = ITMDT::whereIn('ITM', $itemCodes)
            ->whereNotIn('BC_OUT', function ($query) {
                $query->select('BC_OUT')->from('sts');
            })
            ->select('ITM', 'BC_OUT')
            ->get()
            ->groupBy('ITM');

        $results = [];
        foreach ($items as $item) {
            $units = $availableUnits->get($item->ITM, collect());
            $bcs = $units->pluck('BC_OUT')->values()->toArray();

            $results[] = [
                'itm'             => $item->ITM,
                'des'             => $item->DES ?? $item->ITM,
                'hrg'             => (float) ($item->HRG ?? 0),
                'available_count' => count($bcs),
                'available_bcs'   => $bcs,
            ];
        }

        return response()->json([
            'success' => true,
            'items'   => $results
        ]);
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

        // 1. Cek di tabel ITMDT khusus berdasarkan BC_OUT apa adanya
        $itmdt = ITMDT::where('BC_OUT', $code)->first();

        // Jika datanya tidak ketemu, coba hapus char paling depan dan paling belakang
        if (!$itmdt && strlen($code) >= 3) {
            $candidate = substr($code, 1, -1);
            $candidateItmdt = ITMDT::where('BC_OUT', $candidate)->first();
            if ($candidateItmdt) {
                $code = $candidate;
                $itmdt = $candidateItmdt;
            }
        }

        // 2. Cek apakah barcode BC_OUT sudah ada di keranjang
        if (isset($cart[$code])) {
            return redirect()->route('POS.kasir')->with('warning', 'Barcode "' . $code . '" sudah ada di keranjang!');
        }

        // 3. Cek apakah barcode sudah tercatat di tabel STS (sudah terjual)
        if (STS::where('BC_OUT', $code)->exists()) {
            return redirect()->route('POS.kasir')->with('error', 'Barcode "' . $code . '" sudah terjual dan tidak dapat digunakan lagi!');
        }

        if (!$itmdt) {
            return redirect()->route('POS.kasir')->with('error', 'Barcode "' . $code . '" tidak ditemukan!');
        }

        // 4. Ambil data master produk (ITM) berdasarkan ITM milik ITMDT
        $item = ITM::where('ITM', $itmdt->ITM)->first();

        if (!$item) {
            return redirect()->route('POS.kasir')->with('error', 'Master item untuk barcode "' . $code . '" tidak ditemukan!');
        }

        // 5. Tambahkan unit ke keranjang belanja
        $cart[$code] = [
            'id'               => $item->ITM,
            'code'             => $code, // Barcode BC_OUT unik untuk unit barang ini
            'name'             => $item->DES ?? $item->ITM,
            'description'      => $item->mrk()?->DES ?? '',
            'price'            => (float) ($item->HRG ?? 0),
            'qty'              => 1,
            'discount'         => 0,
            'discount_voucher' => 0
        ];

        session()->put('cart', $cart);

        return redirect()->route('POS.kasir')->with('success', 'Produk "' . ($item->DES ?? $code) . '" (Barcode: ' . $code . ') berhasil ditambahkan!');
    }

    /**
     * Memproses transaksi pembayaran dan menyimpan data ke tabel ACR, STS, serta update KAS
     */
    public function bayar(Request $request)
    {
        $cart = $request->input('cart', session()->get('cart', []));
        $voucherAmount = max(0, (float) ($request->input('voucher', session()->get('voucher', 0))));
        $discountPercent = min(100, max(0, (float) ($request->input('discount_percent', session()->get('discount_percent', 0)))));
        $ppnInput = $request->input('ppn', session()->get('ppn', null));
        $ppnPercent = ($ppnInput !== null && $ppnInput !== '') ? min(100, max(0, (int) $ppnInput)) : null;
        $paymentMethod = $request->input('payment_method', 'tunai');
        $bankName = $request->input('bank_name', null);
        $nominalBayar = (float) ($request->input('nominal_bayar', 0));
        $pelangganInput = trim($request->input('pelanggan', ''));
        $salesInput = trim($request->input('sales', ''));

        if (empty($cart) || !is_array($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Keranjang masih kosong!'
            ], 400);
        }

        // Hitung Subtotal dan Total Transaksi (Termasuk Diskon & Voucher per Item)
        $subtotal = 0;
        $totalItemDiscountNominal = 0;
        foreach ($cart as $item) {
            $itemPrice = max(0, (float) ($item['price'] ?? 0));
            $itemDiscountPct = min(100, max(0, (float) ($item['discount'] ?? 0)));
            $itemDiscountVoucher = max(0, (float) ($item['discount_voucher'] ?? 0));
            $qty = max(1, (int) ($item['qty'] ?? 1));

            $discFromPct = $itemPrice * ($itemDiscountPct / 100);
            $priceAfterDiscount = max(0, $itemPrice - $discFromPct - $itemDiscountVoucher);

            $subtotal += ($priceAfterDiscount * $qty);
            $totalItemDiscountNominal += (($discFromPct + $itemDiscountVoucher) * $qty);
        }

        $diskonTransaksiNominal = $subtotal * ($discountPercent / 100);
        $totalSetelahDiskon = max(0, $subtotal - $voucherAmount - $diskonTransaksiNominal);
        $ppnNominal = ($ppnPercent !== null && $ppnPercent > 0) ? ($totalSetelahDiskon * ($ppnPercent / 100)) : 0;
        $grandTotal = $totalSetelahDiskon + $ppnNominal;

        try {
            DB::beginTransaction();

            // 0. Pastikan ada shift kasir yang aktif
            $kas = $this->kasAktif();
            if (!$kas) {
                throw new \Exception('Kasir belum dibuka atau sudah ditutup.');
            }

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

            // Dapatkan Customer CST
            $cst = 'CST0000001';
            $cstObj = null;
            if ($pelangganInput) {
                $foundCst = CST::where('DES', $pelangganInput)->orWhere('CST', $pelangganInput)->first();
                if ($foundCst) {
                    $cst = $foundCst->CST;
                    $cstObj = $foundCst;
                }
            } else {
                $cstObj = CST::first();
                $cst = $cstObj?->CST ?? 'CST0000001';
            }

            // Dapatkan Sales SLS
            $sls = 'SLS0000001';
            if ($salesInput) {
                $foundSls = SLS::where('DES', $salesInput)->orWhere('SLS', $salesInput)->first();
                if ($foundSls) {
                    $sls = $foundSls->SLS;
                }
            } else {
                $sls = SLS::first()?->SLS ?? 'SLS0000001';
            }

            $user = auth()->user()?->USERNAME ?? 'admin';

            // 2. Simpan Header Transaksi ke Tabel ACR
            $isTunai = in_array(strtolower($paymentMethod), ['tunai', 'cash']);
            $totalAllPotongan = $totalItemDiscountNominal + $voucherAmount + $diskonTransaksiNominal;
            $payAmount = $isTunai ? max($grandTotal, $nominalBayar) : $grandTotal;

            ACR::create([
                'REF_NO'  => $refNo,
                'DT'      => now()->toDateString(),
                'CST'     => substr($cst, 0, 10),
                'SLS'     => substr($sls, 0, 10),
                'BRT'     => $grandTotal,
                'PAY'     => $payAmount,
                'TOT_POT' => $totalAllPotongan,
                'JNS'     => $isTunai ? 1 : 2,
                'POT_1'   => (int) $discountPercent,
                'POT_2'   => null,
                'POT_R'   => ($voucherAmount + $totalItemDiscountNominal) > 0 ? ($voucherAmount + $totalItemDiscountNominal) : null,
                'USER'    => substr($user, 0, 10),
            ]);

            // 3. Simpan Rincian Barang ke Tabel STS (Setiap unit barang memiliki BC_OUT berbeda)
            foreach ($cart as $key => $item) {
                $bcOut = substr($item['code'] ?? $key, 0, 20);
                $itmCode = substr($item['id'] ?? $bcOut, 0, 20);
                $hgj = (float) ($item['price'] ?? 0);
                $pot1 = (int) ($item['discount'] ?? 0);
                $potR = (float) ($item['discount_voucher'] ?? 0);
                $brtItem = max(0, $hgj - ($hgj * ($pot1 / 100)) - $potR);

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
                    'POT_R'  => $potR > 0 ? $potR : null,
                    'PPN'    => ($ppnPercent !== null && $ppnPercent > 0) ? $ppnPercent : null,
                    'BRT'    => $brtItem,
                ]);

                // Kurangi STOK di tabel ITM agar tetap sinkron (jumlah ITMDT - jumlah STS)
                ITM::where('ITM', $itmCode)->where('STOK', '>', 0)->decrement('STOK');
            }

            // 4. Update Saldo Penjualan Kas di Tabel KAS (hanya shift aktif)
            if ($isTunai) {
                $kas->DBT  = ($kas->DBT ?? 0) + $grandTotal;
                $kas->AKHT = ($kas->AWLT ?? 0) + $kas->DBT - ($kas->CRT ?? 0);
            } else {
                $kas->DBNT  = ($kas->DBNT ?? 0) + $grandTotal;
                $kas->AKHNT = ($kas->AWLNT ?? 0) + $kas->DBNT - ($kas->CRNT ?? 0);
            }
            $kas->save();

            // Kosongkan keranjang setelah berhasil dibayar
            session()->forget(['cart', 'voucher', 'discount_percent', 'ppn']);

            DB::commit();

            $kembalian = max(0, $nominalBayar - $grandTotal);

            $dsObj = ($cstObj && $cstObj->DS) ? CST::where('CST', $cstObj->DS)->first() : null;
            $customerNama = $cstObj?->DES ?? ($pelangganInput ?: '-');
            $customerAlamat = $cstObj?->AL ?? '-';
            $customerDesa = $dsObj?->DES ?? ($cstObj && empty($cstObj->DS) ? $cstObj->DES : '-');

            return response()->json([
                'success'          => true,
                'message'          => 'Pembayaran berhasil disimpan.',
                'ref_no'           => $refNo,
                'subtotal'         => $subtotal + $totalItemDiscountNominal,
                'item_discount'    => $totalItemDiscountNominal,
                'discount_percent' => $discountPercent,
                'discount_global'  => $diskonTransaksiNominal,
                'voucher'          => $voucherAmount,
                'ppn_percent'      => $ppnPercent,
                'ppn_nominal'      => $ppnNominal,
                'grand_total'      => $grandTotal,
                'dibayar'          => $nominalBayar,
                'kembalian'        => $kembalian,
                'kasir'            => $user,
                'customer_nama'    => $customerNama,
                'customer_alamat'  => $customerAlamat,
                'customer_desa'    => $customerDesa,
                'tanggal'          => now()->translatedFormat('d M Y'),
                'waktu'            => now()->format('H.i'),
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
        $voucher = max(0, (float) $request->input('voucher', 0));
        $discountPercent = min(100, max(0, (float) $request->input('discount_percent', 0)));
        $ppn = $request->input('ppn');
        $ppnValue = ($ppn !== null && $ppn !== '') ? min(100, max(0, (int) $ppn)) : null;

        if (is_array($cart)) {
            foreach ($cart as $k => $item) {
                if (isset($item['discount'])) {
                    $cart[$k]['discount'] = min(100, max(0, (float) $item['discount']));
                }
                if (isset($item['discount_voucher'])) {
                    $cart[$k]['discount_voucher'] = max(0, (float) $item['discount_voucher']);
                }
            }
        }

        session()->put('cart', $cart);
        session()->put('voucher', $voucher);
        session()->put('discount_percent', $discountPercent);
        if ($ppnValue !== null) {
            session()->put('ppn', $ppnValue);
        } else {
            session()->forget('ppn');
        }

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
        session()->forget(['cart', 'voucher', 'discount_percent', 'ppn']);
        return redirect()->route('POS.kasir')->with('info', 'Keranjang berhasil dikosongkan.');
    }
}