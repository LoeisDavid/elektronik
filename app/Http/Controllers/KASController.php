<?php

namespace App\Http\Controllers;

use App\Models\KAS;
use Illuminate\Http\Request;

class KASController extends Controller
{
    public function index(Request $request)
    {
        try {
            $tanggalDari   = trim((string) $request->query('tanggal_dari', ''));
            $tanggalSampai = trim((string) $request->query('tanggal_sampai', ''));
            if ($tanggalDari === '' && $tanggalSampai === '' && $request->filled('tanggal')) {
                $tanggalDari = $tanggalSampai = trim((string) $request->query('tanggal', ''));
            }
            if ($tanggalDari !== '' && $tanggalSampai !== '' && $tanggalDari > $tanggalSampai) {
                [$tanggalDari, $tanggalSampai] = [$tanggalSampai, $tanggalDari];
            }
            $kasir   = trim((string) $request->query('kasir', ''));
            $page    = max(1, (int) $request->query('page', 1));
            $perPage = 4;

            // ------------------------------------------------------------
            // 1. Ambil semua kas dari model KAS (tanpa dengan limit)
            // ------------------------------------------------------------
            $semuaRow = [];

            foreach (KAS::all() as $kas) {
                $user = $kas->user(); // object user

                $kasAwalTunai   = (int) $kas->AWLT;
                $penjualanTunai = (int) $kas->DBT;
                $penjualanNon   = (int) $kas->DBNT;
                $totalPenjualan = $penjualanTunai + $penjualanNon;
                $pengeluaran    = (int) $kas->CRT;
                $uangAkhirTunai = (int) $kas->AKHT;

                $sudahTutup = !empty($kas->TGLK);

                // Selisih cuma hitung uang tunai doang:
                // Kas akhir tunai seharusnya = kas awal tunai + penjualan tunai - pengeluaran tunai
                $kasAkhirTunaiSeharusnya = $kasAwalTunai + $penjualanTunai - $pengeluaran;
                $selisih = $sudahTutup
                    ? $uangAkhirTunai - $kasAkhirTunaiSeharusnya
                    : null;

                $semuaRow[] = (object) [
                    'cd'               => $kas->CD,
                    'kode_user'        => $kas->USER,
                    'kasir'            => $user->name ?? $kas->USER,
                    'tanggal'          => is_object($kas->TGL) ? $kas->TGL->format('Y-m-d') : date('Y-m-d', strtotime($kas->TGL)),
                    'waktu_buka'       => $kas->TGLM,
                    'waktu_tutup'      => $kas->TGLK,
                    'sudah_tutup'      => $sudahTutup,
                    'kas_awal'         => $kasAwalTunai,
                    'tunai'            => $penjualanTunai,
                    'non_tunai'        => $penjualanNon,
                    'total_penjualan'  => $totalPenjualan,
                    'uang_akhir_tunai' => $uangAkhirTunai,
                    'selisih'          => $selisih,
                ];
            }

            // Urutkan terbaru (waktu buka) di atas
            usort($semuaRow, fn ($a, $b) => strtotime($b->waktu_buka) <=> strtotime($a->waktu_buka));

            // ------------------------------------------------------------
            // 2. Daftar pilihan kasir
            // ------------------------------------------------------------
            $daftarKasir = [];
            foreach ($semuaRow as $row) {
                $daftarKasir[$row->kode_user] = $row->kasir;
            }
            asort($daftarKasir);

            // ------------------------------------------------------------
            // 3. Data untuk Tabel Laporan (Ambil semua data, jika difilter tanggal dari-sampai/kasir sesuaikan)
            // ------------------------------------------------------------
            $hasilTabel = array_values(array_filter($semuaRow, function ($row) use ($tanggalDari, $tanggalSampai, $kasir) {
                if ($tanggalDari !== '' && $row->tanggal < $tanggalDari) {
                    return false;
                }
                if ($tanggalSampai !== '' && $row->tanggal > $tanggalSampai) {
                    return false;
                }
                if ($kasir !== '' && (string) $row->kode_user !== $kasir) {
                    return false;
                }
                return true;
            }));

            // ------------------------------------------------------------
            // 4. Statistik 4 Card Atas
            // Default HARI INI jika tanggal kosong. Mengikuti tanggal dari-sampai & kasir jika difilter.
            // ------------------------------------------------------------
            $adaFilterTanggal = $tanggalDari !== '' || $tanggalSampai !== '';
            $hasilStat = array_values(array_filter($semuaRow, function ($row) use ($adaFilterTanggal, $tanggalDari, $tanggalSampai, $kasir) {
                if ($adaFilterTanggal) {
                    if ($tanggalDari !== '' && $row->tanggal < $tanggalDari) {
                        return false;
                    }
                    if ($tanggalSampai !== '' && $row->tanggal > $tanggalSampai) {
                        return false;
                    }
                } else {
                    if ($row->tanggal !== date('Y-m-d')) {
                        return false;
                    }
                }
                if ($kasir !== '' && (string) $row->kode_user !== $kasir) {
                    return false;
                }
                return true;
            }));

            $totalPenjualan = array_sum(array_map(fn ($r) => $r->total_penjualan, $hasilStat));
            $totalTunai     = array_sum(array_map(fn ($r) => $r->tunai, $hasilStat));
            $totalNonTunai  = array_sum(array_map(fn ($r) => $r->non_tunai, $hasilStat));

            $persenTunai    = $totalPenjualan > 0 ? round($totalTunai / $totalPenjualan * 100) : 0;
            $persenNonTunai = $totalPenjualan > 0 ? 100 - $persenTunai : 0;

            if ($tanggalDari !== '' && $tanggalSampai !== '') {
                $statLabel = $tanggalDari === $tanggalSampai
                    ? date('d/m/Y', strtotime($tanggalDari))
                    : date('d/m/Y', strtotime($tanggalDari)) . ' - ' . date('d/m/Y', strtotime($tanggalSampai));
            } elseif ($tanggalDari !== '') {
                $statLabel = 'Mulai ' . date('d/m/Y', strtotime($tanggalDari));
            } elseif ($tanggalSampai !== '') {
                $statLabel = 'Sampai ' . date('d/m/Y', strtotime($tanggalSampai));
            } else {
                $statLabel = 'Hari ini (' . date('d/m/Y') . ')';
            }

            if ($kasir !== '') {
                $statLabel .= ' - ' . ($daftarKasir[$kasir] ?? $kasir);
            }

            $stat = [
                'total_penjualan'   => $totalPenjualan,
                'jumlah_transaksi'  => count($hasilStat),
                'total_kas_tunai'   => $totalTunai,
                'persen_kas_tunai'  => $persenTunai,
                'total_non_tunai'   => $totalNonTunai,
                'persen_non_tunai'  => $persenNonTunai,
                'tanggal_label'     => $statLabel,
            ];

            // ------------------------------------------------------------
            // 5. Pagination Tabel PHP murni
            // ------------------------------------------------------------
            $totalData  = count($hasilTabel);
            $totalPages = max(1, (int) ceil($totalData / $perPage));
            $page       = min($page, $totalPages);
            $offset     = ($page - 1) * $perPage;

            $laporan = array_slice($hasilTabel, $offset, $perPage);
     
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
                'tanggalDari',
                'tanggalSampai',
                'kasir'
            ));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan laporan kas.');
        }
    }
}