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
        'Sukamaju',
        'Cisauk',
        'Mekarwangi',
        'Bojonggede',
        'Ciputat',
        'Margahayu',
        'Sedayu',
        'Gondanglegi',
        'Babakan',
        'Sumberarum',
        'Kalirejo',
        'Wonosobo',
        'Karanganyar',
        'Ngaglik',
        'Singosari',
        'Pagedangan',
        'Cibadak',
        'Tambun',
        'Cikarang',
        'Pondok Gede',
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
            'DS' => sprintf('DS%07d', $num), // fits string(15) PK
            'DES' => substr($villageName, 0, 50), // fits string(50) unique
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
