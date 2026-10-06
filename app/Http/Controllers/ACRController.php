<?php

namespace App\Http\Controllers;

use App\Models\ACR;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ACRController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = ACR::query();

            if ($request->filled('keyword')) {
                $keyword = $request->keyword;
                $query->where('REF_NO', 'LIKE', "%{$keyword}%")
                    //   ->orWhere('BRT', 'LIKE', "%{$keyword}%")
                    //   ->orWhere('DT', 'LIKE', "%{$keyword}%")
                    ;
            }

            if ($request->sort == 'terbaru') {
                $query->orderBy('DT', 'desc');
            } else if($request->sort == 'terlama') {
                $query->orderBy('DT', 'asc');
            } else if($request->sort == 'termahal') {
                $query->orderBy('BRT', 'desc');
            } else if($request->sort == 'termurah') {
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
        }

        return view('POS.transaksi', compact('data'));
    }

    /**
     * [TAMBAHAN PRINT]
     * Membuat PDF struk dengan DomPDF dari data yang dikirim Blade (POST).
     * Tidak ada query ulang ke database.
     */
    public function print(Request $request)
    {
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
                    'nama'     => (string) ($d['nama'] ?? '-'),
                    'qty'      => (int) ($d['qty'] ?? 1),
                    'subtotal' => isset($d['subtotal']) && $d['subtotal'] !== null
                                    ? (float) $d['subtotal']
                                    : null,
                ];
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
            'total'           => (float) $request->input('total'),
            'dibayar'         => $request->filled('dibayar') ? (float) $request->input('dibayar') : null,
            'kembalian'       => $request->filled('kembalian') ? (float) $request->input('kembalian') : null,
            'items'           => $items,
        ];

        // Lebar 80mm (226.77pt), tinggi menyesuaikan jumlah item
        $tinggi = 420 + (count($items) * 36);

            $namaFile = 'struk-' . preg_replace('/[^A-Za-z0-9_-]/', '', $struk['ref_no']) . '.pdf';

            return $pdf->stream($namaFile);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan struk');
        }
    }
}