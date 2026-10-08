<?php

namespace Database\Factories;

use App\Models\KTG;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KTG>
 */
class KTGFactory extends Factory
{
    protected $model = KTG::class;
    protected static int $sequence = 1;

    protected static array $categories = [
        'TV & Audio',
        'Peralatan Dapur',
        'Pendingin Ruangan',
        'Smartphone & Gadget',
        'Komputer & Laptop',
        'Fotografi & Keamanan',
        'Perawatan Rumah',
        'Perangkat Pintar',
        'Aksesoris Elektronik',
        'Peralatan Kebersihan',
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
        $idx = ($num - 1) % count(static::$categories);
        $catName = static::$categories[$idx];
        if ($num > count(static::$categories)) {
            $catName .= ' ' . ceil($num / count(static::$categories));
        }

        return [
            'KTG' => sprintf('KTG%07d', $num), // 10 chars, fits string(10)
            'DES' => substr($catName, 0, 45), // fits string(45)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

