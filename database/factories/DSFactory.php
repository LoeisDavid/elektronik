<?php

namespace Database\Factories;

use App\Models\DS;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DS>
 */
class DSFactory extends Factory
{
    protected $model = DS::class;
    protected static int $sequence = 1;

    protected static array $villages = [
        'Kolor',
        'Pabian',
        'Bangselok',
        'Pamolokan',
        'Marengan Daya',
        'Marengan Laok',
        'Kalianget Timur',
        'Kalianget Barat',
        'Guluk-Guluk',
        'Saronggi',
        'Batuan',
        'Lenteng Barat',
        'Ambunten Timur',
        'Gapura Tengah',
        'Bluto',
        'Manding Laok',
        'Dungkek',
        'Talango',
        'Pasongsongan',
        'Rubaru',
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
        $idx = ($num - 1) % count(static::$villages);
        $villageName = static::$villages[$idx];
        if ($num > count(static::$villages)) {
            $villageName .= ' ' . ceil($num / count(static::$villages));
        }

        return [
            'CST' => sprintf('DS%07d', $num), // Primary key di tabel cst
            'DES' => substr($villageName, 0, 50),
            'AL' => null,
            'TLP' => null,
            'DS' => null, // Desa di tabel cst ditandai dengan DS = null
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
