<?php

namespace App\Http\Controllers;

use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\RP;
use App\Models\RPD;
use App\Models\STS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RPController extends Controller
{
    private const PER_PAGE = 10;

    const OPSI_SORT = [
        'terbaru'        => 'Terbaru',
        'terlama'        => 'Terlama',
        'stok-banyak'    => 'Stok Terbanyak',
        'stok-sedikit'   => 'Stok Tersedikit',
        'status'         => 'Status',
    ];

    /**
     * Menampilkan daftar riwayat perpindahan gudang (RP).
     */
    public function index(Request $request)
    {
        try {
            $keyword = trim((string) $request->query('keyword', ''));
            $sort    = (string) $request->query('sort', 'terbaru');
            if (!isset(self::OPSI_SORT[$sort])) {
                $sort = 'terbaru';
            }

            $gudangs = GD::orderBy('DES')->get();

            // Query dasar RP dengan relasi
            $query = RP::query()
                ->with(['gudangAsal', 'gudangTujuan', 'userRequest', 'userConfirm1', 'userConfirm2'])
                ->withCount('rpd');

            // Pencarian
            if ($keyword !== '') {
                $query->where(function ($q) use ($keyword) {
                    $q->where('RP', 'LIKE', "%{$keyword}%")
                      ->orWhere('DTRQ', 'LIKE', "%{$keyword}%")
                      ->orWhere('SS', 'LIKE', "%{$keyword}%")
                      ->orWhere('RQ', 'LIKE', "%{$keyword}%")
                      ->orWhereHas('gudangAsal', function ($sub) use ($keyword) {
                          $sub->where('DES', 'LIKE', "%{$keyword}%");
                      })
                      ->orWhereHas('gudangTujuan', function ($sub) use ($keyword) {
                          $sub->where('DES', 'LIKE', "%{$keyword}%");
                      })
                      ->orWhereHas('rpd', function ($sub) use ($keyword) {
                          $sub->where('BC_OUT', 'LIKE', "%{$keyword}%");
                      });
                });
            }

            // Pengurutan
            switch ($sort) {
                case 'terlama':
                    $query->orderBy('DTRQ', 'asc')->orderBy('created_at', 'asc');
                    break;
                case 'stok-banyak':
                    $query->orderBy('rpd_count', 'desc')->orderBy('created_at', 'desc');
                    break;
                case 'stok-sedikit':
                    $query->orderBy('rpd_count', 'asc')->orderBy('created_at', 'desc');
                    break;
                case 'status':
                    $query->orderBy('SS', 'asc')->orderBy('created_at', 'desc');
                    break;
                case 'terbaru':
                default:
                    $query->orderBy('DTRQ', 'desc')->orderBy('created_at', 'desc');
                    break;
            }

            $rpList = $query->paginate(self::PER_PAGE)->withQueryString();

            return view('Inventory.perpindahan', [
                'rpList'    => $rpList,
                'gudangs'   => $gudangs,
                'keyword'   => $keyword,
                'sort'      => $sort,
                'opsiSort'  => self::OPSI_SORT,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan ketika menampilkan daftar perpindahan: ' . $e->getMessage());
        }
    }

    /**
     * Menyimpan request perpindahan baru (Pindah Barang).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'DTRQ'      => 'required|date',
            'GD_TUJUAN' => 'required|string|exists:gd,GD',
            'items'     => 'required|array|min:1',
            'items.*'   => 'required|string',
        ], [
            'DTRQ.required'      => 'Tanggal perpindahan wajib diisi.',
            'DTRQ.date'          => 'Format tanggal tidak valid.',
            'GD_TUJUAN.required' => 'Pilih gudang tujuan.',
            'items.required'     => 'Minimal harus ada 1 barang yang dipindahkan.',
            'items.min'          => 'Minimal harus ada 1 barang yang dipindahkan.',
        ]);

        $items = array_values(array_unique(array_filter($validated['items'])));

        if (empty($items)) {
            return redirect()->back()->withInput()->with('error', 'Daftar barang perpindahan tidak boleh kosong.');
        }

        try {
            $currentUser = Auth::user()?->USERNAME ?? 'admin';
            $rpCode = null;

            DB::transaction(function () use ($validated, $items, $currentUser, &$rpCode) {
                $gdAsal = null;
                $namaGdTujuan = GD::where('GD', $validated['GD_TUJUAN'])->value('DES') ?? $validated['GD_TUJUAN'];

                // Validasi setiap barcode
                foreach ($items as $bcOut) {
                    $itmdt = ITMDT::where('BC_OUT', $bcOut)->first();

                    if (!$itmdt) {
                        throw ValidationException::withMessages([
                            'items' => "Barang dengan barcode {$bcOut} tidak ditemukan di sistem."
                        ]);
                    }

                    // Syarat 1: Lokasi barang saat ini tidak boleh ada di gudang tujuan
                    if ($itmdt->GD === $validated['GD_TUJUAN']) {
                        throw ValidationException::withMessages([
                            'items' => "Barang {$bcOut} saat ini sudah berada di gudang tujuan ({$namaGdTujuan}). Lokasi barang saat ini tidak boleh sama dengan gudang tujuan."
                        ]);
                    }

                    // Gudang asal diambil otomatis dari ITMDT.GD barang
                    if ($gdAsal === null) {
                        $gdAsal = $itmdt->GD;
                    } elseif ($itmdt->GD !== $gdAsal) {
                        $namaGdCurrent = GD::where('GD', $gdAsal)->value('DES') ?? $gdAsal;
                        $namaGdItem = GD::where('GD', $itmdt->GD)->value('DES') ?? $itmdt->GD;
                        throw ValidationException::withMessages([
                            'items' => "Barang {$bcOut} berada di gudang {$namaGdItem}, sedangkan barang sebelumnya berada di {$namaGdCurrent}. Barang dalam satu request perpindahan harus berasal dari gudang yang sama."
                        ]);
                    }

                    // Cek apakah sudah terjual
                    if (STS::where('BC_OUT', $bcOut)->exists()) {
                        throw ValidationException::withMessages([
                            'items' => "Barang {$bcOut} sudah berstatus terjual."
                        ]);
                    }

                    // Cek apakah sedang dalam proses perpindahan lain yang belum selesai
                    $adaPerpindahanAktif = RPD::where('BC_OUT', $bcOut)
                        ->whereIn('SS', ['Ready', 'Dikirim', 'Request', 'Pending'])
                        ->exists();

                    if ($adaPerpindahanAktif) {
                        throw ValidationException::withMessages([
                            'items' => "Barang {$bcOut} sedang dalam proses perpindahan lain yang belum selesai."
                        ]);
                    }
                }

                $rpCode = $this->generateRpCode();

                // Simpan header RP dengan GD_ASAL otomatis dari ITMDT.GD
                RP::create([
                    'RP'        => $rpCode,
                    'DTRQ'      => $validated['DTRQ'],
                    'DTCF1'     => null,
                    'DTCF2'     => null,
                    'GD_ASAL'   => $gdAsal,
                    'GD_TUJUAN' => $validated['GD_TUJUAN'],
                    'SS'        => 'Request',
                    'RQ'        => $currentUser,
                    'CF1'       => null,
                    'CF2'       => null,
                ]);

                // Simpan baris detail RPD (Status awal: Ready)
                foreach ($items as $bcOut) {
                    $rpdCode = $this->generateRpdCode();
                    RPD::create([
                        'RPD'    => $rpdCode,
                        'RP'     => $rpCode,
                        'BC_OUT' => $bcOut,
                        'SS'     => 'Ready',
                    ]);
                }
            });

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Request perpindahan berhasil disimpan dengan kode {$rpCode}.",
                    'rp'      => $rpCode,
                ]);
            }

            return redirect()->route('inventory.perpindahan')
                ->with('success', "Request perpindahan barang {$rpCode} berhasil dibuat.");
        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->validator->errors()->first(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan perpindahan: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Memeriksa barcode saat discan pada form modal (AJAX).
     */
    public function checkBarcode(Request $request)
    {
        $bcOut         = trim((string) $request->query('bc_out', $request->input('bc_out')));
        $gdTujuan      = trim((string) $request->query('gd_tujuan', $request->input('gd_tujuan')));
        $currentGdAsal = trim((string) $request->query('current_gd_asal', $request->input('current_gd_asal')));

        if ($bcOut === '') {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak boleh kosong.',
            ], 400);
        }

        $itmdt = ITMDT::where('BC_OUT', $bcOut)->first();

        if (!$itmdt) {
            return response()->json([
                'success' => false,
                'message' => "Barcode {$bcOut} tidak ditemukan di database stok.",
            ], 404);
        }

        // Cek apakah barang sudah terjual
        if (STS::where('BC_OUT', $bcOut)->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Barang {$bcOut} sudah berstatus terjual.",
            ], 422);
        }

        // Syarat 1: Lokasi barang saat ini (ITMDT.GD) tidak boleh ada di gudang tujuan
        if ($gdTujuan !== '' && $itmdt->GD === $gdTujuan) {
            $namaGdTujuan = GD::where('GD', $gdTujuan)->value('DES') ?? $gdTujuan;
            return response()->json([
                'success' => false,
                'message' => "Barang {$bcOut} saat ini sudah berada di gudang tujuan ({$namaGdTujuan}). Lokasi barang saat ini tidak boleh ada di gudang tujuan.",
            ], 422);
        }

        // Jika sudah ada barang sebelumnya dalam batch ini, pastikan gudang asal sama
        if ($currentGdAsal !== '' && $itmdt->GD !== $currentGdAsal) {
            $namaGdItem = GD::where('GD', $itmdt->GD)->value('DES') ?? $itmdt->GD;
            $namaCurrent = GD::where('GD', $currentGdAsal)->value('DES') ?? $currentGdAsal;
            return response()->json([
                'success' => false,
                'message' => "Barang {$bcOut} berada di gudang '{$namaGdItem}', sedangkan barang sebelumnya berada di '{$namaCurrent}'. Semua barang dalam satu dokumen harus dari gudang asal yang sama.",
            ], 422);
        }

        // Cek apakah sedang dalam proses perpindahan lain yang belum selesai
        $adaPerpindahanAktif = RPD::where('BC_OUT', $bcOut)
            ->whereIn('SS', ['Ready', 'Dikirim', 'Request', 'Pending'])
            ->exists();

        if ($adaPerpindahanAktif) {
            return response()->json([
                'success' => false,
                'message' => "Barang {$bcOut} sedang dalam proses perpindahan lain.",
            ], 422);
        }

        $itm = ITM::where('ITM', $itmdt->ITM)->first();
        $gd  = GD::where('GD', $itmdt->GD)->first();

        return response()->json([
            'success' => true,
            'item'    => [
                'bc_out' => $itmdt->BC_OUT,
                'itm'    => $itmdt->ITM,
                'des'    => $itm?->DES ?? $itmdt->ITM,
                'gd'     => $itmdt->GD,
                'gd_des' => $gd?->DES ?? $itmdt->GD,
            ],
        ]);
    }

    /**
     * Mengambil daftar barang yang tersedia di gudang asal (autocomplete/dropdown bantuan).
     */
    public function getAvailableItems(Request $request)
    {
        $gdAsal = trim((string) $request->query('gd_asal'));
        $q      = trim((string) $request->query('q'));

        if ($gdAsal === '') {
            return response()->json(['items' => []]);
        }

        // Ambil BC_OUT di gudang ini yang belum terjual dan belum di-request
        $query = DB::table('itmdt')
            ->join('itm', 'itm.ITM', '=', 'itmdt.ITM')
            ->leftJoin('gd', 'gd.GD', '=', 'itmdt.GD')
            ->where('itmdt.GD', $gdAsal)
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))->from('sts')->whereColumn('sts.BC_OUT', 'itmdt.BC_OUT');
            })
            ->whereNotExists(function ($sub) {
                $sub->select(DB::raw(1))->from('rpd')
                    ->whereColumn('rpd.BC_OUT', 'itmdt.BC_OUT')
                    ->whereIn('rpd.SS', ['Ready', 'Dikirim', 'Request', 'Pending']);
            })
            ->select('itmdt.BC_OUT', 'itmdt.ITM', 'itm.DES', 'gd.DES as GD_DES');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('itmdt.BC_OUT', 'LIKE', "%{$q}%")
                    ->orWhere('itm.DES', 'LIKE', "%{$q}%")
                    ->orWhere('itmdt.ITM', 'LIKE', "%{$q}%");
            });
        }

        $items = $query->limit(30)->get();

        return response()->json(['items' => $items]);
    }

    /**
     * Menampilkan detail perpindahan (halaman detail blade atau respon JSON).
     */
    public function show(Request $request, string $id)
    {
        $rp = RP::with(['gudangAsal', 'gudangTujuan', 'userRequest', 'userConfirm1', 'userConfirm2', 'rpd'])
            ->where('RP', $id)
            ->firstOrFail();

        $keyword = trim((string) $request->query('keyword', ''));

        $itemsQuery = DB::table('rpd')
            ->join('itmdt', 'itmdt.BC_OUT', '=', 'rpd.BC_OUT')
            ->leftJoin('itm', 'itm.ITM', '=', 'itmdt.ITM')
            ->leftJoin('mrk', 'mrk.MRK', '=', 'itm.MRK')
            ->leftJoin('ktg', 'ktg.KTG', '=', 'itm.KTG')
            ->where('rpd.RP', $id)
            ->select(
                'rpd.RPD',
                'rpd.BC_OUT',
                'rpd.SS',
                'rpd.updated_at',
                'itm.DES as NAMA_BARANG',
                'mrk.DES as NAMA_MERK',
                'ktg.DES as NAMA_KATEGORI'
            );

        if ($keyword !== '') {
            $itemsQuery->where(function ($q) use ($keyword) {
                $q->where('rpd.BC_OUT', 'LIKE', "%{$keyword}%")
                  ->orWhere('itm.DES', 'LIKE', "%{$keyword}%")
                  ->orWhere('ktg.DES', 'LIKE', "%{$keyword}%")
                  ->orWhere('rpd.SS', 'LIKE', "%{$keyword}%");
            });
        }

        if ($request->wantsJson() || $request->ajax()) {
            $items = $itemsQuery->get();
            return response()->json([
                'success' => true,
                'rp'      => [
                    'RP'           => $rp->RP,
                    'DTRQ'         => $rp->DTRQ,
                    'DTCF1'        => $rp->DTCF1,
                    'DTCF2'        => $rp->DTCF2,
                    'GD_ASAL'      => $rp->GD_ASAL,
                    'GD_ASAL_NAMA' => $rp->gudangAsal?->DES ?? $rp->GD_ASAL,
                    'GD_TUJUAN'    => $rp->GD_TUJUAN,
                    'GD_TUJUAN_NAMA'=> $rp->gudangTujuan?->DES ?? $rp->GD_TUJUAN,
                    'SS'           => $rp->SS,
                    'RQ'           => $rp->RQ,
                    'RQ_NAMA'      => $rp->userRequest?->DES ?? $rp->RQ,
                    'CF1'          => $rp->CF1,
                    'CF1_NAMA'     => $rp->userConfirm1?->DES ?? $rp->CF1,
                    'CF2'          => $rp->CF2,
                    'CF2_NAMA'     => $rp->userConfirm2?->DES ?? $rp->CF2,
                    'created_at'   => $rp->created_at?->format('Y-m-d H:i:s'),
                    'total_items'  => $items->count(),
                ],
                'items'   => $items,
            ]);
        }

        // Ambil semua item untuk scanning konfirmasi di modal
        $allItems = DB::table('rpd')
            ->join('itmdt', 'itmdt.BC_OUT', '=', 'rpd.BC_OUT')
            ->leftJoin('itm', 'itm.ITM', '=', 'itmdt.ITM')
            ->leftJoin('mrk', 'mrk.MRK', '=', 'itm.MRK')
            ->leftJoin('ktg', 'ktg.KTG', '=', 'itm.KTG')
            ->where('rpd.RP', $id)
            ->select(
                'rpd.RPD',
                'rpd.BC_OUT',
                'rpd.SS',
                'rpd.updated_at',
                'itm.DES as NAMA_BARANG',
                'mrk.DES as NAMA_MERK',
                'ktg.DES as NAMA_KATEGORI'
            )
            ->get();

        $itemsPaginated = $itemsQuery->paginate(5)->withQueryString();

        return view('Inventory.perpindahan-detail', [
            'rp'             => $rp,
            'itemsPaginated' => $itemsPaginated,
            'allItems'       => $allItems,
            'keyword'        => $keyword,
        ]);
    }

    /**
     * Konfirmasi 1: Barang dikonfirmasi untuk dikirim dari gudang asal (status menjadi 'Pengiriman', rpd 'Dikirim').
     */
    public function konfirmasi1(Request $request, string $id)
    {
        $rp = RP::where('RP', $id)->firstOrFail();

        if ($rp->SS !== 'Request') {
            return redirect()->back()->with('error', "Perpindahan {$id} tidak dalam status 'Request'.");
        }

        try {
            DB::transaction(function () use ($rp) {
                $now = date('Y-m-d');
                $currentUser = Auth::user()?->USERNAME ?? 'admin';

                $rp->update([
                    'DTCF1' => $now,
                    'CF1'   => $currentUser,
                    'SS'    => 'Pengiriman',
                ]);

                RPD::where('RP', $rp->RP)->update(['SS' => 'Dikirim']);
            });

            return redirect()->back()
                ->with('success', "Konfirmasi 1 pengiriman ({$id}) berhasil disimpan. Status perpindahan saat ini 'Pengiriman'.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses konfirmasi 1: ' . $e->getMessage());
        }
    }

    /**
     * Konfirmasi 2: Penerimaan barang di gudang tujuan dengan validasi scanning.
     * Semua barang yang dikirim harus discan (berubah jadi 'Diterima') atau jika tidak terscan diinputkan 'Rusak' atau 'Hilang'.
     */
    public function konfirmasi2(Request $request, string $id)
    {
        $rp = RP::where('RP', $id)->firstOrFail();

        if (!in_array($rp->SS, ['Pengiriman', 'Pending'])) {
            return redirect()->back()->with('error', "Perpindahan {$id} belum dalam tahap pengiriman atau sudah selesai.");
        }

        $rpdList = RPD::where('RP', $rp->RP)->get();
        if ($rpdList->isEmpty()) {
            return redirect()->back()->with('error', "Tidak ada barang dalam perpindahan {$id}.");
        }

        $itemsInput = $request->input('items', []);
        $validStatuses = ['Diterima', 'Rusak', 'Hilang'];
        $unresolved = [];

        foreach ($rpdList as $rpd) {
            $st = $itemsInput[$rpd->BC_OUT] ?? null;
            if (!$st || !in_array($st, $validStatuses)) {
                $unresolved[] = $rpd->BC_OUT;
            }
        }

        if (!empty($unresolved)) {
            return redirect()->back()->with('error', 'Semua barang yang dikirim harus discan atau ditentukan statusnya (Rusak atau Hilang) sebelum konfirmasi kedua dapat diselesaikan. Terdapat ' . count($unresolved) . ' barang yang belum ditentukan.');
        }

        try {
            DB::transaction(function () use ($rp, $rpdList, $itemsInput) {
                $now = date('Y-m-d');
                $currentUser = Auth::user()?->USERNAME ?? 'admin';

                $totalDiterima = 0;
                $totalRusak = 0;
                $totalHilang = 0;

                foreach ($rpdList as $rpd) {
                    $newStatus = $itemsInput[$rpd->BC_OUT];
                    $rpd->update(['SS' => $newStatus]);

                    if ($newStatus === 'Diterima') {
                        $totalDiterima++;
                        // Pindahkan stok barang ke gudang tujuan
                        ITMDT::where('BC_OUT', $rpd->BC_OUT)->update(['GD' => $rp->GD_TUJUAN]);
                    } elseif (in_array($newStatus, ['Rusak', 'Hilang'])) {
                        if ($newStatus === 'Rusak') $totalRusak++;
                        if ($newStatus === 'Hilang') $totalHilang++;

                        // Kurangi STOK di tabel ITM karena barang rusak/hilang
                        $itmCode = ITMDT::where('BC_OUT', $rpd->BC_OUT)->value('ITM');
                        if ($itmCode) {
                            ITM::where('ITM', $itmCode)->where('STOK', '>', 0)->decrement('STOK');
                        }
                    }
                }

                $updateData = [
                    'DTCF2' => $now,
                    'CF2'   => $currentUser,
                    'SS'    => 'Diterima',
                ];
                if (!$rp->DTCF1) {
                    $updateData['DTCF1'] = $now;
                    $updateData['CF1']   = $currentUser;
                }

                $rp->update($updateData);
            });

            return redirect()->route('inventory.perpindahan.show', $rp->RP)
                ->with('success', "Konfirmasi 2 penerimaan ({$id}) berhasil disimpan. Semua barang telah diproses.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses konfirmasi 2: ' . $e->getMessage());
        }
    }

    /**
     * Menolak request perpindahan barang (hanya jika masih berstatus 'Request').
     */
    public function tolak(Request $request, string $id)
    {
        $rp = RP::where('RP', $id)->firstOrFail();

        if ($rp->SS !== 'Request') {
            return redirect()->back()->with('error', "Perpindahan {$id} tidak dalam status 'Request' sehingga tidak dapat ditolak.");
        }

        try {
            DB::transaction(function () use ($rp) {
                $rp->update([
                    'SS' => 'Ditolak',
                ]);

                RPD::where('RP', $rp->RP)->update(['SS' => 'Ditolak']);
            });

            return redirect()->back()
                ->with('success', "Request perpindahan {$id} berhasil ditolak.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menolak perpindahan: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus request perpindahan (hanya jika masih berstatus 'Request').
     */
    public function destroy(string $id)
    {
        $rp = RP::where('RP', $id)->firstOrFail();

        if ($rp->SS !== 'Request') {
            return redirect()->back()->with('error', "Perpindahan {$id} sudah diproses dan tidak dapat dihapus.");
        }

        try {
            DB::transaction(function () use ($rp) {
                RPD::where('RP', $rp->RP)->delete();
                $rp->delete();
            });

            return redirect()->route('inventory.perpindahan')
                ->with('success', "Request perpindahan {$id} berhasil dihapus.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus perpindahan: ' . $e->getMessage());
        }
    }

    /**
     * Generate kode unik untuk RP (maks 15 karakter).
     */
    private function generateRpCode(): string
    {
        $prefix = 'RP' . date('ymd');
        $count  = DB::table('rp')->where('RP', 'like', $prefix . '%')->count();

        do {
            $count++;
            $code = $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        } while (DB::table('rp')->where('RP', $code)->exists());

        return $code;
    }

    /**
     * Generate kode unik untuk RPD (maks 15 karakter).
     */
    private function generateRpdCode(): string
    {
        $prefix = 'RPD' . date('ymd');
        $count  = DB::table('rpd')->where('RPD', 'like', $prefix . '%')->count();

        do {
            $count++;
            $code = $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
        } while (DB::table('rpd')->where('RPD', $code)->exists());

        return $code;
    }

    /**
     * Halaman cetak laporan perpindahan stok (bisa dicetak saat status Request, Pengiriman, atau Diterima).
     */
    public function cetak(string $id)
    {
        $rp = RP::with(['gudangAsal', 'gudangTujuan', 'userRequest', 'userConfirm1', 'userConfirm2'])
            ->where('RP', $id)
            ->firstOrFail();

        $items = DB::table('rpd')
            ->join('itmdt', 'itmdt.BC_OUT', '=', 'rpd.BC_OUT')
            ->leftJoin('itm', 'itm.ITM', '=', 'itmdt.ITM')
            ->leftJoin('mrk', 'mrk.MRK', '=', 'itm.MRK')
            ->leftJoin('ktg', 'ktg.KTG', '=', 'itm.KTG')
            ->where('rpd.RP', $id)
            ->select(
                'rpd.RPD',
                'rpd.BC_OUT',
                'rpd.SS',
                'rpd.updated_at',
                'itm.DES as NAMA_BARANG',
                'mrk.DES as NAMA_MERK',
                'ktg.DES as NAMA_KATEGORI'
            )
            ->get();

        $petugas = auth()->user()?->USERNAME ?? 'admin';
        $tanggalCetak = now()->locale('id')->translatedFormat('d F Y');
        $lokasi = 'Sumenep';

        // Hitung ringkasan status detail
        $countTotal = $items->count();
        $countDiterima = $items->where('SS', 'Diterima')->count();
        $countRusak = $items->where('SS', 'Rusak')->count();
        $countHilang = $items->where('SS', 'Hilang')->count();
        $countDikirim = $items->where('SS', 'Dikirim')->count();
        $countReady = $items->where('SS', 'Ready')->count();

        return view('Inventory.perpindahan-print', compact(
            'rp',
            'items',
            'petugas',
            'tanggalCetak',
            'lokasi',
            'countTotal',
            'countDiterima',
            'countRusak',
            'countHilang',
            'countDikirim',
            'countReady'
        ));
    }
}

