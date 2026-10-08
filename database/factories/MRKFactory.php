<?php

namespace Database\Factories;

use App\Models\MRK;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MRK>
 */
class MRKFactory extends Factory
{
    protected $model = MRK::class;
    protected static int $sequence = 1;

    protected static array $brands = [
        'Samsung',
        'LG',
        'Polytron',
        'Sharp',
        'Panasonic',
        'Xiaomi',
        'Cosmos',
        'Miyako',
        'Toshiba',
        'Philips',
        'Realme',
        'Asus',
        'Sony',
        'Aqua Japan',
        'Daikin',
        'Modena',
        'Mito',
        'Changhong',
        'Denpoo',
        'Sanex',
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
        $idx = ($num - 1) % count(static::$brands);
        $brandName = static::$brands[$idx];
        if ($num > count(static::$brands)) {
            $brandName .= ' ' . ceil($num / count(static::$brands));
        }

        return [
            'MRK' => sprintf('MRK%07d', $num), // 10 chars, fits string(10)
            'DES' => substr($brandName, 0, 45), // fits string(45)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

