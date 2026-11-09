<?php

namespace Database\Seeders;

use App\Models\GD;
use App\Models\ITMDT;
use App\Models\RP;
use App\Models\RPD;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeder untuk membuat dummy data perpindahan gudang (RP & RPD).
 * Membuat beberapa dokumen perpindahan dengan berbagai status:
 * - Request (barang Ready)
 * - Pengiriman (barang Dikirim) — siap untuk dikonfirmasi 2 via scanning
 * - Diterima (barang Diterima/Rusak/Hilang)
 * - Ditolak
 */
class RPSeeder extends Seeder
{
    public function run(): void
    {
        $username = 'admin';
        $gudangs  = GD::pluck('GD')->toArray();

        if (count($gudangs) < 2) {
            $this->command->warn('Butuh minimal 2 gudang. Seeder dilewati.');
            return;
        }

        // Ambil seluruh BC_OUT yang tersedia (belum terjual dan belum dalam perpindahan aktif)
        $available = DB::table('itmdt')
            ->leftJoin('sts', 'sts.BC_OUT', '=', 'itmdt.BC_OUT')
            ->whereNull('sts.REC')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('rpd')
                  ->join('rp', 'rp.RP', '=', 'rpd.RP')
                  ->whereColumn('rpd.BC_OUT', 'itmdt.BC_OUT')
                  ->whereIn('rp.SS', ['Request', 'Pengiriman']);
            })
            ->select('itmdt.BC_OUT', 'itmdt.GD')
            ->get()
            ->groupBy('GD');

        // Konfigurasi dokumen perpindahan yang akan dibuat
        $scenarios = [
            ['ss' => 'Request',    'cf1' => false, 'cf2' => false],
            ['ss' => 'Pengiriman', 'cf1' => true,  'cf2' => false],
            ['ss' => 'Pengiriman', 'cf1' => true,  'cf2' => false],
            ['ss' => 'Diterima',   'cf1' => true,  'cf2' => true],
            ['ss' => 'Ditolak',    'cf1' => false, 'cf2' => false],
        ];

        $rpCounter  = RP::count();
        $rpdCounter = RPD::count();

        foreach ($scenarios as $idx => $scenario) {
            // Pilih gudang asal yang punya item cukup
            $gdAsal = null;
            $itemsForTransfer = collect();

            foreach ($available as $gd => $items) {
                if ($items->count() >= 5) {
                    $gdAsal = $gd;
                    // Ambil 5 sampai min(20, jumlah tersedia)
                    $take = min(rand(5, 20), $items->count());
                    $itemsForTransfer = $items->random($take);
                    break;
                }
            }

            if (!$gdAsal || $itemsForTransfer->isEmpty()) {
                $this->command->warn("Skenario #{$idx}: tidak cukup item tersedia. Dilewati.");
                continue;
            }

            // Pilih gudang tujuan yang berbeda dari gudang asal
            $gdTujuanOptions = array_values(array_diff($gudangs, [$gdAsal]));
            $gdTujuan = $gdTujuanOptions[array_rand($gdTujuanOptions)];

            // Generate kode RP
            $rpCounter++;
            $rpCode = 'RP' . date('ymd') . str_pad($rpCounter, 4, '0', STR_PAD_LEFT);

            // Tanggal
            $dtRequest = now()->subDays(rand(3, 14))->toDateString();
            $dtCf1 = $scenario['cf1'] ? now()->subDays(rand(1, 3))->toDateString() : null;
            $dtCf2 = $scenario['cf2'] ? now()->toDateString() : null;

            DB::transaction(function () use (
                $rpCode, $dtRequest, $dtCf1, $dtCf2, $gdAsal, $gdTujuan,
                $scenario, $username, $itemsForTransfer, &$rpdCounter, &$available
            ) {
                RP::create([
                    'RP'        => $rpCode,
                    'DTRQ'      => $dtRequest,
                    'DTCF1'     => $dtCf1,
                    'DTCF2'     => $dtCf2,
                    'GD_ASAL'   => $gdAsal,
                    'GD_TUJUAN' => $gdTujuan,
                    'SS'        => $scenario['ss'],
                    'RQ'        => $username,
                    'CF1'       => $scenario['cf1'] ? $username : null,
                    'CF2'       => $scenario['cf2'] ? $username : null,
                ]);

                foreach ($itemsForTransfer as $item) {
                    $rpdCounter++;
                    $rpdCode = 'RPD' . date('ymd') . str_pad($rpdCounter, 4, '0', STR_PAD_LEFT);

                    RPD::create([
                        'RPD'    => $rpdCode,
                        'RP'     => $rpCode,
                        'BC_OUT' => $item->BC_OUT,
                    ]);

                    // Jika status Diterima pada skenario selesai, pindahkan ITMDT.GD
                    if ($scenario['ss'] === 'Diterima') {
                        ITMDT::where('BC_OUT', $item->BC_OUT)->update(['GD' => $gdTujuan]);
                    }
                }

                // Hapus item yang sudah dipakai dari pool available
                $usedBcOuts = $itemsForTransfer->pluck('BC_OUT')->toArray();
                if (isset($available[$gdAsal])) {
                    $available[$gdAsal] = $available[$gdAsal]->reject(function ($i) use ($usedBcOuts) {
                        return in_array($i->BC_OUT, $usedBcOuts);
                    });
                }
            });

            $count = $itemsForTransfer->count();
            $this->command->info("✓ {$rpCode}: {$scenario['ss']} ({$count} barang) — {$gdAsal} → {$gdTujuan}");
        }
    }
}
