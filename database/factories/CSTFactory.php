<?php

namespace Database\Factories;

use App\Models\CST;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CST>
 */
class CSTFactory extends Factory
{
    protected $model = CST::class;
    protected static int $sequence = 1;

    protected static array $customers = [
        'Budi Santoso',
        'Toko Terang Jaya',
        'Agus Setiawan',
        'Dewi Lestari',
        'Hendra Wijaya',
        'PT Digital Solusindo',
        'Bambang Susilo',
        'Rina Wijaya',
        'Siti Aminah',
        'Joko Widodo',
        'Toko Abadi Elektronik',
        'CV Cahaya Mandiri',
        'Eko Prasetyo',
        'Indah Permata',
        'Rahmat Hidayat',
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
        $idx = ($num - 1) % count(static::$customers);
        $cstName = static::$customers[$idx];
        if ($num > count(static::$customers)) {
            $cstName .= ' (' . $num . ')';
        }

        return [
            'CST' => sprintf('CST%07d', $num), // 10 chars, fits string(10)
            'DES' => substr($cstName, 0, 150),
            'TLP' => substr('08' . fake()->numerify('##########'), 0, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

