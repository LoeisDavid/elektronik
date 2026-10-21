<?php

namespace App\Http\Controllers;

use App\Models\ITM;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpnameStokController extends Controller
{
    /**
     * Halaman pembukaan (memilih barang yang mau dicek).
     */
    public function index()
    {
        $daftarItem = ITM::orderBy('DES')->get(['ITM', 'DES']);

        return view('inventory.opname-stok', compact('daftarItem'));
    }

    /**
     * Dipanggil saat tombol "Mulai" ditekan dengan memilih ITM tertentu.
     */
    public function pull(Request $request)
    {
        $itm = $request->query('itm') ?? $request->input('itm');

        if (!$itm) {
            return redirect()
                ->route('inventory.opname')
                ->with('error', 'Silakan pilih barang yang ingin dicek stoknya terlebih dahulu.');
        }

        try {
            // Jaga-jaga untuk data besar supaya tidak kena batas 30 detik PHP
            set_time_limit(120);

            $selectedItem = ITM::where('ITM', $itm)->first();
            if (!$selectedItem) {
                return redirect()
                    ->route('inventory.opname')
                    ->with('error', 'Barang tidak ditemukan.');
            }

            $items = DB::table('itmdt as t1')
                ->leftJoin('itm as t2', 't1.ITM', '=', 't2.ITM')
                ->where('t1.ITM', $itm)
                ->select(
                    't1.BC_OUT as kode_barcode',
                    't2.DES as nama_barang',
                    DB::raw('EXISTS (SELECT 1 FROM sts WHERE sts.BC_OUT = t1.BC_OUT) AS terjual')
                )
                ->get()
                ->map(fn ($row) => [
                    'kode_barcode' => (string) $row->kode_barcode,
                    'nama_barang'  => $row->nama_barang,
                    'status'       => $row->terjual ? 'sudah_terjual' : 'checking',
                ])
                ->values();

            return view('inventory.opname-stok', [
                'items'        => $items,
                'selectedItem' => $selectedItem,
            ]);
        } catch (\Throwable $e) {
            report($e); // catat error ke log supaya mudah dilacak

            return redirect()
                ->route('inventory.opname')
                ->with('error', 'Terjadi kesalahan ketika menampilkan data cek stok');
        }
    }

    /**
     * Halaman cetak laporan hasil opname stok.
     */
    public function cetak(Request $request)
    {
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        $tanggal = date('j') . ' ' . $bulanIndo[(int)date('n')] . ' ' . date('Y');
        $petugas = auth()->user()->USERNAME ?? 'admin';

        if ($request->isMethod('post') && $request->filled('payload')) {
            $payload = json_decode($request->input('payload'), true) ?? [];

            $tanggal = $payload['tanggal'] ?? $tanggal;
            $petugas = $payload['petugas'] ?? $petugas;
            $lokasi  = $payload['lokasi'] ?? $lokasi;

            $rawItems = collect($payload['items'] ?? []);
            $items = $rawItems->map(fn ($row) => [
                'kode_barcode' => (string) ($row['kode_barcode'] ?? ''),
                'nama_barang'  => $row['nama_barang'] ?? '-',
                'status'       => $row['status'] ?? 'checking',
            ]);

            $cTotal    = $items->count();
            $cSudah    = $items->where('status', 'sudah_terjual')->count();
            $cBelum    = $items->where('status', 'belum_terjual')->count();
            $cChecking = $items->where('status', 'checking')->count();

            $summary = [
                'total'         => $payload['summary']['total'] ?? $cTotal,
                'sudah_terjual' => $payload['summary']['sudah_terjual'] ?? $cSudah,
                'belum_terjual' => $payload['summary']['belum_terjual'] ?? $cBelum,
                'tidak_terscan' => $payload['summary']['tidak_terscan'] ?? $cChecking,
            ];

            $pdf = Pdf::loadView('Inventory.cetak-opname', compact('items', 'summary', 'tanggal', 'petugas', 'lokasi'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('laporan-cek-stok-' . date('YmdHis') . '.pdf');
        }

        // Fallback jika dibuka via GET dengan parameter itm
        $itm = $request->query('itm');
        if ($itm) {
            $items = DB::table('itmdt as t1')
                ->leftJoin('itm as t2', 't1.ITM', '=', 't2.ITM')
                ->where('t1.ITM', $itm)
                ->select(
                    't1.BC_OUT as kode_barcode',
                    't2.DES as nama_barang',
                    DB::raw('EXISTS (SELECT 1 FROM sts WHERE sts.BC_OUT = t1.BC_OUT) AS terjual')
                )
                ->get()
                ->map(fn ($row) => [
                    'kode_barcode' => (string) $row->kode_barcode,
                    'nama_barang'  => $row->nama_barang,
                    'status'       => $row->terjual ? 'sudah_terjual' : 'checking',
                ]);

            $cTotal    = $items->count();
            $cSudah    = $items->where('status', 'sudah_terjual')->count();
            $cBelum    = $items->where('status', 'belum_terjual')->count();
            $cChecking = $cTotal - $cSudah - $cBelum;

            $summary = [
                'total'         => $cTotal,
                'sudah_terjual' => $cSudah,
                'belum_terjual' => $cBelum,
                'tidak_terscan' => $cChecking,
            ];

            $pdf = Pdf::loadView('Inventory.cetak-opname', compact('items', 'summary', 'tanggal', 'petugas', 'lokasi'))
                ->setPaper('a4', 'portrait');

            return $pdf->stream('laporan-cek-stok-' . date('YmdHis') . '.pdf');
        }

        return redirect()->route('inventory.opname');
    }
}