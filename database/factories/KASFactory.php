<?php

namespace Database\Factories;

use App\Models\KAS;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KAS>
 */
class KASFactory extends Factory
{
    protected $model = KAS::class;
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
        $awlt = 500000;
        $dbt = fake()->numberBetween(10, 50) * 100000;
        $dbnt = fake()->numberBetween(5, 20) * 100000;
        $crt = fake()->numberBetween(0, 2) * 50000;
        $akht = $awlt + $dbt - $crt;
        $tgl = fake()->dateTimeBetween('-30 days', 'now');

        // Waktu realistis yang bervariasi (bukan 08:00/17:00 yang seragam):
        // buka antara 07:00-10:00, tutup 8-11 jam kemudian
        $bukaJam   = fake()->numberBetween(7, 10);
        $bukaMenit = fake()->numberBetween(0, 59);
        $bukaDetik = fake()->numberBetween(0, 59);
        $durasiJam = fake()->numberBetween(8, 11);
        $tutupTambahan = $durasiJam * 3600 + fake()->numberBetween(0, 3599);

        $waktuBuka  = (clone $tgl)->setTime($bukaJam, $bukaMenit, $bukaDetik);
        $waktuTutup = (clone $waktuBuka)->modify("+{$tutupTambahan} seconds");

        return [
            'CD'         => sprintf('CD%08d', $num),
            'TGL'        => $tgl->format('Y-m-d'),
            'USER'       => 'admin',
            'AWLT'       => $awlt,
            'AWLNT'      => 0,
            'DBT'        => $dbt,
            'DBNT'       => $dbnt,
            'CRT'        => $crt,
            'CRNT'       => 0,
            'AKHT'       => $akht,
            'AKHNT'      => $dbnt,
            'TGLM'       => $waktuBuka->format('Y-m-d H:i:s'),
            'TGLK'       => $waktuTutup->format('Y-m-d H:i:s'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
