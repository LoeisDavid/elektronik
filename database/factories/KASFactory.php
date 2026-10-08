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
            'TGLM'       => $tgl->format('Y-m-d 08:00:00'),
            'TGLK'       => $tgl->format('Y-m-d 17:00:00'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
