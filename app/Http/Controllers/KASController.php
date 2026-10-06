<?php

namespace App\Http\Controllers;

use App\Models\KAS;
use Illuminate\Http\Request;

class KASController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tanggal = trim((string) $request->query('tanggal', ''));
            $kasir   = trim((string) $request->query('kasir', ''));
            $page    = max(1, (int) $request->query('page', 1));
            $perPage = 10;
     
            // ------------------------------------------------------------
            // 1. Ambil semua kas (tanpa with), lalu akses ->user() per baris
            // ------------------------------------------------------------
            $semuaRow = [];
     
            foreach (KAS::all() as $kas) {
                $user = $kas->user(); // object user
     
                $kasAwal        = (int) $kas->AWLT + (int) $kas->AWLNT;
                $penjualanTunai = (int) $kas->DBT;
                $penjualanNon   = (int) $kas->DBNT;
                $totalPenjualan = $penjualanTunai + $penjualanNon;
                $pengeluaran    = (int) $kas->CRT + (int) $kas->CRNT;
                $kasAkhirRill   = (int) $kas->AKHT + (int) $kas->AKHNT;
     
                $sudahTutup = !empty($kas->TGLK);
     
                // Selisih = kas akhir rill - kas akhir seharusnya
                // (kas awal + pemasukan - pengeluaran). Null bila shift belum ditutup.
                $selisih = $sudahTutup
                    ? $kasAkhirRill - ($kasAwal + $totalPenjualan - $pengeluaran)
                    : null;
     
                $semuaRow[] = (object) [
                    'kode_user'       => $kas->USER,
                    'kasir'           => $user->name ?? $kas->USER,
                    'tanggal'         => date('Y-m-d', strtotime($kas->TGL)),
                    'waktu_buka'      => $kas->TGLM,
                    'waktu_tutup'     => $kas->TGLK,
                    'sudah_tutup'     => $sudahTutup,
                    'kas_awal'        => $kasAwal,
                    'tunai'           => $penjualanTunai,
                    'non_tunai'       => $penjualanNon,
                    'total_penjualan' => $totalPenjualan,
                    'kas_akhir_rill'  => $kasAkhirRill,
                    'selisih'         => $selisih,
                ];
            }
     
            // Urutkan terbaru (waktu buka) di atas
            usort($semuaRow, fn ($a, $b) => strtotime($b->waktu_buka) <=> strtotime($a->waktu_buka));
     
            // ------------------------------------------------------------
            // 2. Daftar pilihan kasir (diambil dari seluruh data, sebelum filter)
            // ------------------------------------------------------------
            $daftarKasir = [];
            foreach ($semuaRow as $row) {
                $daftarKasir[$row->kode_user] = $row->kasir;
            }
            asort($daftarKasir);
     
            // ------------------------------------------------------------
            // 3. Filter PHP murni
            // ------------------------------------------------------------
            $hasil = array_values(array_filter($semuaRow, function ($row) use ($tanggal, $kasir) {
                if ($tanggal !== '' && $row->tanggal !== $tanggal) {
                    return false;
                }
                if ($kasir !== '' && (string) $row->kode_user !== $kasir) {
                    return false;
                }
                return true;
            }));
     
            // ------------------------------------------------------------
            // 4. Statistik (dari data hasil filter)
            // ------------------------------------------------------------
            $totalPenjualan = array_sum(array_map(fn ($r) => $r->total_penjualan, $hasil));
            $totalTunai     = array_sum(array_map(fn ($r) => $r->tunai, $hasil));
            $totalNonTunai  = array_sum(array_map(fn ($r) => $r->non_tunai, $hasil));
     
            $persenTunai    = $totalPenjualan > 0 ? round($totalTunai / $totalPenjualan * 100) : 0;
            $persenNonTunai = $totalPenjualan > 0 ? 100 - $persenTunai : 0;
     
            $stat = [
                'total_penjualan'   => $totalPenjualan,
                'jumlah_transaksi'  => count($hasil),
                'total_kas_tunai'   => $totalTunai,
                'persen_kas_tunai'  => $persenTunai,
                'total_non_tunai'   => $totalNonTunai,
                'persen_non_tunai'  => $persenNonTunai,
                'tanggal_label'     => $tanggal !== '' ? date('d/m/Y', strtotime($tanggal)) : 'Semua tanggal',
            ];
     
            // ------------------------------------------------------------
            // 5. Pagination PHP murni
            // ------------------------------------------------------------
            $totalData  = count($hasil);
            $totalPages = max(1, (int) ceil($totalData / $perPage));
            $page       = min($page, $totalPages);
            $offset     = ($page - 1) * $perPage;
     
            $laporan = array_slice($hasil, $offset, $perPage);
     
            $pagination = [
                'page'        => $page,
                'per_page'    => $perPage,
                'total_data'  => $totalData,
                'total_pages' => $totalPages,
                'dari'        => $totalData > 0 ? $offset + 1 : 0,
                'sampai'      => $offset + count($laporan),
            ];
     
            return view('POS.laporan', compact(
                'laporan',
                'stat',
                'pagination',
                'daftarKasir',
                'tanggal',
                'kasir'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan laporan kas.');
        }
    }
}