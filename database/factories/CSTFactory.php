<?php

namespace Database\Factories;

use App\Models\CST;
use App\Models\DS;
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
            'CST' => sprintf('CST%07d', $num), // 10 chars, fits string(10) PK
            'DES' => substr($cstName, 0, 150), // fits string(150) unique
            'AL' => substr(fake('id_ID')->address(), 0, 100), // fits string(100)
            'TLP' => sprintf('0812%08d', $num), // fits string(20) unique & numeric
            'DS' => DS::factory(), // references string(15) DS PK
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

