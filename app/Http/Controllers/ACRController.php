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

                foreach ($acr->sts() as $item) {
                    if (!isset($detail[$item->ITM])) {
                        $detail[$item->ITM] = [
                            'nama' => $item->itm()->DES,
                            'qty' => 1,
                        ];
                    } else {
                        $detail[$item->ITM]['qty']++;
                    }
                }

                $acr->detail = array_values($detail);
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan data transaksi');
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
        try {
            $request->validate([
                'ref_no'    => 'required|string|max:50',
                'tanggal'   => 'nullable|string|max:50',
                'kasir'     => 'nullable|string|max:50',
                'metode'    => 'nullable|string|max:30',
                'total'     => 'required|numeric',
                'dibayar'   => 'nullable|numeric',
                'kembalian' => 'nullable|numeric',
                'items'     => 'required|string',
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

            $struk = [
                'ref_no'    => $request->input('ref_no'),
                'tanggal'   => $request->input('tanggal'),
                'kasir'     => $request->input('kasir'),
                'metode'    => $request->input('metode'),
                'total'     => (float) $request->input('total'),
                'dibayar'   => $request->filled('dibayar') ? (float) $request->input('dibayar') : null,
                'kembalian' => $request->filled('kembalian') ? (float) $request->input('kembalian') : null,
                'items'     => $items,
            ];

            // Lebar 80mm (226.77pt), tinggi menyesuaikan jumlah item
            $tinggi = 380 + (count($items) * 32);

            $pdf = Pdf::loadView('POS.struk-print', ['struk' => $struk])
                ->setPaper([0, 0, 226.77, $tinggi], 'portrait');

            $namaFile = 'struk-' . preg_replace('/[^A-Za-z0-9_-]/', '', $struk['ref_no']) . '.pdf';

            return $pdf->stream($namaFile);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan struk');
        }
    }
}