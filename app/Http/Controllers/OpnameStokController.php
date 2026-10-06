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
     */
    public function pull()
    {
        try {
            $items = DB::select("
                SELECT
                    t1.BC_OUT as kode_barcode,
                    t2.DES as nama_barang,
                    t3.BC_OUT as status
                FROM itmdt t1
                LEFT JOIN itm t2 ON t1.ITM = t2.ITM
                LEFT JOIN sts t3 ON t1.BC_OUT = t3.BC_OUT
            ");

            // Status awal hanya 'checking' atau 'sudah_terjual'.
            // Pengurutan dilakukan di JavaScript (halaman opname-stok).
            foreach ($items as $row) {
                $row->status = $row->status ? 'sudah_terjual' : 'checking';
            }

            return view('inventory.opname-stok', ['items' => $items]);
        } catch (\Throwable $e) {
            report($e); // catat error ke log supaya mudah dilacak

            return redirect()
                ->route('inventory.opname')
                ->with('error', 'Terjadi kesalahan ketika menampilkan data cek stok');
        }
    }
}