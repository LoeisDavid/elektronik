<?php

namespace App\Http\Controllers;

use App\Models\ACR;
use App\Models\STS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ACRController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ACR::query();

            // ---------------------------------------------------------
            // FILTER KEYWORD
            // ---------------------------------------------------------
            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where('REF_NO', 'LIKE', "%{$keyword}%");
            }

            // ---------------------------------------------------------
            // FILTER TANGGAL (DARI - SAMPAI)
            // ---------------------------------------------------------
            $dari   = $request->input('tanggal_dari');
            $sampai = $request->input('tanggal_sampai');

            // Kalau user terbalik mengisi (dari > sampai), tukar otomatis
            if ($dari && $sampai && $dari > $sampai) {
                [$dari, $sampai] = [$sampai, $dari];
            }

            if ($dari) {
                $query->whereDate('DT', '>=', $dari);
            }

            if ($sampai) {
                $query->whereDate('DT', '<=', $sampai);
            }

            // ---------------------------------------------------------
            // SORT
            // ---------------------------------------------------------
            if ($request->sort == 'terbaru') {
                $query->orderBy('DT', 'desc');
            } else if ($request->sort == 'terlama') {
                $query->orderBy('DT', 'asc');
            } else if ($request->sort == 'termahal') {
                $query->orderBy('BRT', 'desc');
            } else if ($request->sort == 'termurah') {
                $query->orderBy('BRT', 'asc');
            } else {
                $query->orderBy('DT', 'desc');
            }

            $data = $query->paginate(10)->withQueryString();

            foreach ($data as $acr) {
                $detail = [];
                $rawSubtotal = 0;
                $totalItemDiscount = 0;

                foreach ($acr->sts() as $item) {
                    $itmObj = $item->itm();
                    $nama = $itmObj ? ($itmObj->DES ?? $item->ITM) : $item->ITM;
                    $hgj = (float) ($item->HGJ ?? 0);
                    $pot1 = (float) ($item->POT_1 ?? 0);
                    $potR = (float) ($item->POT_R ?? 0);
                    $discPctNominal = $hgj * ($pot1 / 100);
                    $itemDiscountUnit = $discPctNominal + $potR;
                    $brtItem = (float) ($item->BRT ?? max(0, $hgj - $itemDiscountUnit));

                    $rawSubtotal += $hgj;
                    $totalItemDiscount += $itemDiscountUnit;

                    $discInfo = '';
                    if ($pot1 > 0 && $potR > 0) {
                        $discInfo = "Diskon: -{$pot1}% (-Rp " . number_format($discPctNominal, 0, ',', '.') . ") + Voucher item: -Rp " . number_format($potR, 0, ',', '.');
                    } elseif ($pot1 > 0) {
                        $discInfo = "Diskon: -{$pot1}% (-Rp " . number_format($discPctNominal, 0, ',', '.') . ")";
                    } elseif ($potR > 0) {
                        $discInfo = "Voucher item: -Rp " . number_format($potR, 0, ',', '.');
                    }

                    // Setiap baris/unit item ditampilkan terpisah (tidak dijadikan satu)
                    $detail[] = [
                        'nama'             => $nama,
                        'code'             => $item->BC_OUT ?? $item->ITM,
                        'qty'              => 1,
                        'price'            => $hgj,
                        'discount'         => $pot1,
                        'discount_voucher' => $potR,
                        'discount_info'    => $discInfo,
                        'subtotal'         => $brtItem,
                    ];
                }

                $acr->detail = $detail;
                $acr->subtotal_calc = $rawSubtotal;
                $acr->item_discount_calc = $totalItemDiscount;

                $globalPct = (float) ($acr->POT_1 ?? 0);
                $afterItemDisc = max(0, $rawSubtotal - $totalItemDiscount);
                $globalDiscNominal = $afterItemDisc * ($globalPct / 100);
                $acr->global_discount_calc = $globalDiscNominal;

                $totPot = (float) ($acr->TOT_POT ?? ($acr->POT_R ?? 0));
                $voucherCalc = max(0, $totPot - $totalItemDiscount - $globalDiscNominal);
                $acr->voucher_calc = $voucherCalc;

                $totalSetelahDiskon = max(0, $rawSubtotal - $totalItemDiscount - $globalDiscNominal - $voucherCalc);
                $firstStsWithPpn = $acr->sts()->firstWhere(fn ($s) => $s->PPN !== null);
                $ppnVal = $firstStsWithPpn ? (int) $firstStsWithPpn->PPN : null;
                $acr->ppn = $ppnVal;
                $acr->ppn_nominal = ($ppnVal !== null && $ppnVal > 0) ? ($totalSetelahDiskon * ($ppnVal / 100)) : 0;
            }

            return view('POS.transaksi', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data ACR.');
        }
    }

    /**
     * [TAMBAHAN PRINT]
     * Membuat PDF struk dengan DomPDF dari data yang dikirim Blade (POST).
     * Tidak ada query ulang ke database.
     */
    public function print(Request $request)
    {
        try {
            $request->validate([
                'ref_no'          => 'required|string|max:50',
                'tanggal'         => 'nullable|string|max:50',
                'waktu'           => 'nullable|string|max:20',
                'kasir'           => 'nullable|string|max:50',
                'metode'          => 'nullable|string|max:30',
                'subtotal'        => 'nullable|numeric',
                'discount_item'   => 'nullable|numeric',
                'discount_global' => 'nullable|numeric',
                'voucher'         => 'nullable|numeric',
                'ppn'             => 'nullable|numeric',
                'ppn_nominal'     => 'nullable|numeric',
                'total'           => 'required|numeric',
                'dibayar'         => 'nullable|numeric',
                'kembalian'       => 'nullable|numeric',
                'items'           => 'required|string',
            ]);

            $itemsRaw = json_decode($request->input('items'), true);

            if (!is_array($itemsRaw)) {
                abort(422, 'Data detail struk tidak valid.');
            }

            $items = [];
            foreach ($itemsRaw as $d) {
                if (!is_array($d)) {
                    continue;
                }

                $items[] = [
                    'nama'          => (string) ($d['nama'] ?? '-'),
                    'qty'           => (int) ($d['qty'] ?? 1),
                    'price'         => isset($d['price']) ? (float) $d['price'] : null,
                    'discount_info' => !empty($d['discount_info']) ? (string) $d['discount_info'] : null,
                    'subtotal'      => isset($d['subtotal']) && $d['subtotal'] !== null
                                        ? (float) $d['subtotal']
                                        : null,
                ];
            }

            $struk = [
                'ref_no'          => $request->input('ref_no'),
                'tanggal'         => $request->input('tanggal'),
                'waktu'           => $request->input('waktu'),
                'kasir'           => $request->input('kasir'),
                'metode'          => $request->input('metode'),
                'subtotal'        => $request->filled('subtotal') ? (float) $request->input('subtotal') : null,
                'discount_item'   => $request->filled('discount_item') ? (float) $request->input('discount_item') : 0,
                'discount_global' => $request->filled('discount_global') ? (float) $request->input('discount_global') : 0,
                'voucher'         => $request->filled('voucher') ? (float) $request->input('voucher') : 0,
                'ppn'             => $request->filled('ppn') ? (float) $request->input('ppn') : null,
                'ppn_nominal'     => $request->filled('ppn_nominal') ? (float) $request->input('ppn_nominal') : 0,
                'total'           => (float) $request->input('total'),
                'dibayar'         => $request->filled('dibayar') ? (float) $request->input('dibayar') : null,
                'kembalian'       => $request->filled('kembalian') ? (float) $request->input('kembalian') : null,
                'items'           => $items,
            ];

            // Lebar 80mm (226.77pt), tinggi menyesuaikan jumlah item
            $tinggi = 420 + (count($items) * 36);

            $pdf = Pdf::loadView('POS.struk-print', ['struk' => $struk])
                ->setPaper([0, 0, 226.77, $tinggi], 'portrait');

            $namaFile = 'struk-' . preg_replace('/[^A-Za-z0-9_-]/', '', $struk['ref_no']) . '.pdf';

            return $pdf->stream($namaFile);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan struk');
        }
    }

    /**
     * Halaman edit untuk transaksi: mengubah harga jual, diskon, dan PPN setiap item
     */
    public function edit(string $id)
    {
        try {
            $refNo = urldecode($id);
            $acr = ACR::where('REF_NO', $refNo)->firstOrFail();
            $items = $acr->sts();

            $customer = $acr->cst();
            $sales = $acr->sls();

            // Hitung breakdown saat ini
            $rawSubtotal = 0;
            $totalItemDiscount = 0;
            $ppnPercent = null;

            foreach ($items as $item) {
                $hgj = (float) ($item->HGJ ?? 0);
                $pot1 = (float) ($item->POT_1 ?? 0);
                $potR = (float) ($item->POT_R ?? 0);
                $rawSubtotal += $hgj;
                $discPctNominal = $hgj * ($pot1 / 100);
                $totalItemDiscount += ($discPctNominal + $potR);
                if ($item->PPN !== null && $ppnPercent === null) {
                    $ppnPercent = (int) $item->PPN;
                }
            }

            $globalPct = (float) ($acr->POT_1 ?? 0);
            $afterItemDisc = max(0, $rawSubtotal - $totalItemDiscount);
            $globalDiscNominal = $afterItemDisc * ($globalPct / 100);

            $totPot = (float) ($acr->TOT_POT ?? ($acr->POT_R ?? 0));
            $voucherCalc = max(0, $totPot - $totalItemDiscount - $globalDiscNominal);
            $totalSetelahDiskon = max(0, $afterItemDisc - $voucherCalc - $globalDiscNominal);

            $ppnNominal = ($ppnPercent !== null && $ppnPercent > 0) ? ($totalSetelahDiskon * ($ppnPercent / 100)) : 0;

            return view('POS.transaksi-edit', compact(
                'acr',
                'items',
                'customer',
                'sales',
                'rawSubtotal',
                'totalItemDiscount',
                'globalPct',
                'globalDiscNominal',
                'voucherCalc',
                'ppnPercent',
                'ppnNominal'
            ));
        } catch (\Exception $e) {
            return redirect()->route('POS.transaksi')->with('error', 'Transaksi tidak ditemukan: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui rincian transaksi (harga jual, diskon item, diskon global, dan PPN)
     */
    public function update(Request $request, string $id)
    {
        $refNo = urldecode($id);
        $acr = ACR::where('REF_NO', $refNo)->firstOrFail();

        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.rec'      => 'required|string',
            'items.*.hgj'      => 'required|numeric|min:0',
            'items.*.pot_1'    => 'nullable|numeric|min:0|max:100',
            'items.*.pot_r'    => 'nullable|numeric|min:0',
            'ppn'              => 'nullable|numeric|min:0|max:100',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'voucher'          => 'nullable|numeric|min:0',
        ], [
            'items.*.hgj.required' => 'Harga jual wajib diisi.',
            'items.*.hgj.min'      => 'Harga jual tidak boleh kurang dari 0.',
            'items.*.pot_1.max'    => 'Diskon persen item maksimal 100%.',
            'ppn.max'              => 'PPN maksimal 100%.',
            'discount_percent.max' => 'Diskon global maksimal 100%.',
        ]);

        try {
            DB::beginTransaction();

            // PPN Global: cukup 1 input, semua item STS mengikuti PPN ini
            $globalPpn = $request->filled('ppn') ? min(100, max(0, (int) $request->input('ppn'))) : null;

            $rawSubtotal = 0;
            $totalItemDiscount = 0;
            $itemsData = $request->input('items', []);

            foreach ($itemsData as $data) {
                $rec = $data['rec'];
                $sts = STS::where('REF_NO', $acr->REF_NO)->where('REC', $rec)->firstOrFail();

                $hgj = max(0, (float) ($data['hgj'] ?? 0));
                $pot1 = isset($data['pot_1']) && $data['pot_1'] !== '' ? min(100, max(0, (float) $data['pot_1'])) : null;
                $potR = isset($data['pot_r']) && $data['pot_r'] !== '' ? max(0, (float) $data['pot_r']) : null;

                $discPctNominal = $pot1 ? ($hgj * ($pot1 / 100)) : 0;
                $discNominal = $potR ?? 0;
                $brtItem = max(0, $hgj - $discPctNominal - $discNominal);

                // Update STS dengan harga jual, diskon item, dan PPN global
                $sts->update([
                    'HGJ'   => $hgj,
                    'POT_1' => ($pot1 !== null && $pot1 > 0) ? (int) $pot1 : null,
                    'POT_R' => ($potR !== null && $potR > 0) ? $potR : null,
                    'PPN'   => ($globalPpn !== null && $globalPpn > 0) ? $globalPpn : null,
                    'BRT'   => $brtItem,
                ]);

                $rawSubtotal += $hgj;
                $totalItemDiscount += ($discPctNominal + $discNominal);
            }

            // Diskon transaksi global & Voucher transaksi
            $globalPct = $request->filled('discount_percent') ? min(100, max(0, (float) $request->input('discount_percent'))) : 0;
            $voucherGlobal = $request->filled('voucher') ? max(0, (float) $request->input('voucher')) : 0;

            $subtotalAfterItemDisc = max(0, $rawSubtotal - $totalItemDiscount);
            $globalDiscNominal = $subtotalAfterItemDisc * ($globalPct / 100);
            $totalSetelahDiskon = max(0, $subtotalAfterItemDisc - $voucherGlobal - $globalDiscNominal);

            // PPN dihitung setelah menjumlahkan total semua barang dan setelah mengurangi total barang dari diskon
            $ppnRate = ($globalPpn !== null && $globalPpn > 0) ? $globalPpn : 0;
            $ppnNominal = $ppnRate > 0 ? ($totalSetelahDiskon * ($ppnRate / 100)) : 0;

            $newGrandTotal = $totalSetelahDiskon + $ppnNominal;
            $oldGrandTotal = (float) $acr->BRT;
            $selisih = $newGrandTotal - $oldGrandTotal;

            $totalAllPotongan = $totalItemDiscount + $voucherGlobal + $globalDiscNominal;

            $acr->update([
                'BRT'     => $newGrandTotal,
                'PAY'     => max($newGrandTotal, (float) $acr->PAY + $selisih),
                'TOT_POT' => $totalAllPotongan,
                'POT_1'   => $globalPct > 0 ? (int) $globalPct : null,
                'POT_R'   => ($voucherGlobal + $totalItemDiscount) > 0 ? ($voucherGlobal + $totalItemDiscount) : null,
            ]);

            DB::commit();

            return redirect()->route('POS.transaksi')->with('success', "Transaksi {$acr->REF_NO} berhasil diperbarui.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika memperbarui transaksi: ' . $e->getMessage())->withInput();
        }
    }
}