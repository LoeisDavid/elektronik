<?php

namespace Database\Factories;

use App\Models\GD;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GD>
 */
class GDFactory extends Factory
{
    protected $model = GD::class;
    protected static int $sequence = 1;

    protected static array $warehouses = [
        'Gudang Utama Jakarta',
        'Gudang Surabaya',
        'Gudang Bandung',
        'Gudang Semarang',
        'Gudang Medan',
        'Gudang Makassar',
        'Gudang Bekasi',
        'Gudang Tangerang',
        'Gudang Depok',
        'Gudang Palembang',
    ];

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
        $idx = ($num - 1) % count(static::$warehouses);
        $gdName = static::$warehouses[$idx];
        if ($num > count(static::$warehouses)) {
            $gdName .= ' ' . ceil($num / count(static::$warehouses));
        }

        return [
            'GD' => sprintf('GD%08d', $num), // 10 chars, fits string(10)
            'DES' => substr($gdName, 0, 45), // fits string(45)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

