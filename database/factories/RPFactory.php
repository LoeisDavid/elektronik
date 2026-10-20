<?php

namespace Database\Factories;

use App\Models\GD;
use App\Models\ITMDT;
use App\Models\RP;
use App\Models\RPD;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<RP>
 */
class RPFactory extends Factory
{
    protected $model = RP::class;
    protected static int $sequence = 1;

    public static function resetSequence(int $start = 1): void
    {
        static::$sequence = $start;
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $num = static::$sequence++;
        $code = 'RP' . date('ymd') . str_pad($num, 4, '0', STR_PAD_LEFT);

        // Ambil 2 gudang acak yang berbeda
        $allGudangs = GD::pluck('GD')->toArray();
        if (count($allGudangs) >= 2) {
            $gdAsal = fake()->randomElement($allGudangs);
            $gdTujuanOptions = array_values(array_diff($allGudangs, [$gdAsal]));
            $gdTujuan = fake()->randomElement($gdTujuanOptions);
        } else {
            $gdAsal = 'GD00000001';
            $gdTujuan = 'GD00000002';
        }

        $dtrq = fake()->dateTimeBetween('-14 days', '-3 days')->format('Y-m-d');

        return [
            'RP'        => $code,
            'DTRQ'      => $dtrq,
            'DTCF1'     => null,
            'DTCF2'     => null,
            'GD_ASAL'   => $gdAsal,
            'GD_TUJUAN' => $gdTujuan,
            'SS'        => 'Request',
            'RQ'        => 'admin',
            'CF1'       => null,
            'CF2'       => null,
            'created_at'=> now(),
            'updated_at'=> now(),
        ];
    }

    /**
     * Status Request (semua item Ready)
     */
    public function request(): static
    {
        return $this->state(fn (array $attributes) => [
            'SS'    => 'Request',
            'DTCF1' => null,
            'DTCF2' => null,
            'CF1'   => null,
            'CF2'   => null,
        ]);
    }

    /**
     * Status Pengiriman (semua item Dikirim) - siap dikonfirmasi kedua
     */
    public function pengiriman(): static
    {
        return $this->state(function (array $attributes) {
            $rqDate = $attributes['DTRQ'] ?? now()->subDays(3)->toDateString();
            $cf1Date = fake()->dateTimeBetween($rqDate, 'now')->format('Y-m-d');

            return [
                'SS'    => 'Pengiriman',
                'DTCF1' => $cf1Date,
                'DTCF2' => null,
                'CF1'   => 'admin',
                'CF2'   => null,
            ];
        });
    }

    /**
     * Status Diterima (item Diterima / Rusak / Hilang)
     */
    public function diterima(): static
    {
        return $this->state(function (array $attributes) {
            $rqDate = $attributes['DTRQ'] ?? now()->subDays(5)->toDateString();
            $cf1Date = fake()->dateTimeBetween($rqDate, '-1 days')->format('Y-m-d');
            $cf2Date = now()->toDateString();

            return [
                'SS'    => 'Diterima',
                'DTCF1' => $cf1Date,
                'DTCF2' => $cf2Date,
                'CF1'   => 'admin',
                'CF2'   => 'admin',
            ];
        });
    }

    /**
     * Status Ditolak
     */
    public function ditolak(): static
    {
        return $this->state(fn (array $attributes) => [
            'SS'    => 'Ditolak',
            'DTCF1' => null,
            'DTCF2' => null,
            'CF1'   => null,
            'CF2'   => null,
        ]);
    }

    /**
     * Lampirkan 5-20 barang (RPD) dari gudang asal yang belum terjual
     */
    public function withItems(?int $itemCount = null): static
    {
        return $this->afterCreating(function (RP $rp) use ($itemCount) {
            $count = $itemCount ?? rand(5, 20);

            // Cari item di gudang asal yang belum terjual & belum di RP aktif
            $availableItems = DB::table('itmdt')
                ->where('GD', $rp->GD_ASAL)
                ->leftJoin('sts', 'sts.BC_OUT', '=', 'itmdt.BC_OUT')
                ->whereNull('sts.REC')
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                      ->from('rpd')
                      ->whereColumn('rpd.BC_OUT', 'itmdt.BC_OUT')
                      ->whereIn('rpd.SS', ['Ready', 'Dikirim']);
                })
                ->select('itmdt.BC_OUT')
                ->inRandomOrder()
                ->limit($count)
                ->get();

            // Jika tidak cukup di GD_ASAL, ambil item bebas manapun yang tersedia
            if ($availableItems->count() < 5) {
                $additional = DB::table('itmdt')
                    ->leftJoin('sts', 'sts.BC_OUT', '=', 'itmdt.BC_OUT')
                    ->whereNull('sts.REC')
                    ->whereNotExists(function ($q) {
                        $q->select(DB::raw(1))
                          ->from('rpd')
                          ->whereColumn('rpd.BC_OUT', 'itmdt.BC_OUT')
                          ->whereIn('rpd.SS', ['Ready', 'Dikirim']);
                    })
                    ->select('itmdt.BC_OUT')
                    ->inRandomOrder()
                    ->limit($count)
                    ->get();

                $availableItems = $additional;
                // Update GD_ASAL agar sesuai dengan item pertama jika ada
                if ($availableItems->isNotEmpty()) {
                    $firstGd = DB::table('itmdt')->where('BC_OUT', $availableItems->first()->BC_OUT)->value('GD');
                    if ($firstGd && $firstGd !== $rp->GD_TUJUAN) {
                        $rp->update(['GD_ASAL' => $firstGd]);
                    }
                }
            }

            $currentRpdCount = RPD::count();

            foreach ($availableItems as $item) {
                $currentRpdCount++;
                $rpdCode = 'RPD' . date('ymd') . str_pad($currentRpdCount, 4, '0', STR_PAD_LEFT);

                // Tentukan status RPD berdasarkan status RP
                $detailStatus = match ($rp->SS) {
                    'Request'    => 'Ready',
                    'Pengiriman' => 'Dikirim',
                    'Ditolak'    => 'Ditolak',
                    'Diterima'   => fake()->randomElement(['Diterima', 'Diterima', 'Diterima', 'Diterima', 'Rusak', 'Hilang']),
                    default      => 'Ready',
                };

                RPD::create([
                    'RPD'        => $rpdCode,
                    'RP'         => $rp->RP,
                    'BC_OUT'     => $item->BC_OUT,
                    'SS'         => $detailStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Jika sudah diterima, ubah lokasi gudang di ITMDT
                if ($rp->SS === 'Diterima' && $detailStatus === 'Diterima') {
                    ITMDT::where('BC_OUT', $item->BC_OUT)->update(['GD' => $rp->GD_TUJUAN]);
                }
            }
        });
    }
}
