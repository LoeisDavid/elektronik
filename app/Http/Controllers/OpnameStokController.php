<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class OpnameStokController extends Controller
{
    /**
     * Halaman pembukaan (hanya tombol "Mulai"). Belum ada data yang di-pull.
     */
    public function index()
    {
        return view('inventory.opname-stok'); // tanpa $items -> layar Mulai
    }

    /**
     * Dipanggil saat tombol "Mulai" ditekan: baru di sini data di-pull.
     *
     * Catatan performa:
     * - Status "terjual" dicek dengan EXISTS, bukan LEFT JOIN ke tabel sts.
     *   LEFT JOIN bisa menggandakan baris kalau satu BC_OUT muncul lebih dari
     *   sekali di sts, dan itu yang membuat halaman "memuat terus".
     * - Pastikan kolom sts.BC_OUT dan itmdt.ITM / itm.ITM sudah di-index.
     */
    public function pull()
    {
        try {
            // Jaga-jaga untuk data besar supaya tidak kena batas 30 detik PHP
            set_time_limit(120);

            $items = DB::table('itmdt as t1')
                ->leftJoin('itm as t2', 't1.ITM', '=', 't2.ITM')
                ->select(
                    't1.BC_OUT as kode_barcode',
                    't2.DES as nama_barang',
                    DB::raw('EXISTS (SELECT 1 FROM sts WHERE sts.BC_OUT = t1.BC_OUT) AS terjual')
                )
                ->get()
                // Status awal hanya 'checking' atau 'sudah_terjual'.
                // Pengurutan dilakukan di JavaScript (halaman opname-stok).
                ->map(fn ($row) => [
                    'kode_barcode' => (string) $row->kode_barcode,
                    'nama_barang'  => $row->nama_barang,
                    'status'       => $row->terjual ? 'sudah_terjual' : 'checking',
                ])
                ->values();

            return view('inventory.opname-stok', ['items' => $items]);
        } catch (\Throwable $e) {
            report($e); // catat error ke log supaya mudah dilacak

            return redirect()
                ->route('inventory.opname')
                ->with('error', 'Terjadi kesalahan ketika menampilkan data cek stok');
        }
    }
}