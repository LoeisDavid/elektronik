<?php

namespace Database\Factories;

use App\Models\SPP;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SPP>
 */
class SPPFactory extends Factory
{
    protected $model = SPP::class;
    protected static int $sequence = 1;

    protected static array $suppliers = [
        'PT Satya Jaya Elektronik',
        'CV Sentosa Abadi',
        'PT Mitra Elektronik Indonesia',
        'CV Nusantara Tech',
        'PT Sinar Surya Elektronik',
        'PT Global Distribusi Utama',
        'PT Mega Jaya Tech',
        'CV Electro Utama',
        'PT Indo Tech Makmur',
        'PT Surya Jaya Elektronik',
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
        $idx = ($num - 1) % count(static::$suppliers);
        $sppName = static::$suppliers[$idx];
        if ($num > count(static::$suppliers)) {
            $sppName .= ' ' . ceil($num / count(static::$suppliers));
        }

        return [
            'SPP' => sprintf('SPP%07d', $num), // 10 chars, fits string(10)
            'DES' => substr($sppName, 0, 150),
            'TLP' => substr('08' . fake()->numerify('##########'), 0, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

