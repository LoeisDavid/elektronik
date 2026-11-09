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
        'Toko Terang Jaya Elektronik',
        'Agus Setiawan',
        'Dewi Lestari',
        'Hendra Wijaya',
        'PT Digital Solusindo',
        'Bambang Susilo',
        'Rina Wijaya',
        'Siti Aminah',
        'H. Achmad Zaini',
        'Toko Abadi Elektronik',
        'CV Cahaya Mandiri',
        'Eko Prasetyo',
        'Indah Permata',
        'Rahmat Hidayat',
        'Warung Kopi & Elektronik Berkah',
        'Nurul Hidayati',
        'Drs. H. Mulyadi',
        'Toko Sahabat Listrik',
        'Suryadi Pratama',
    ];

    public static function resetSequence(int $start = 1): void
    {
        static::$sequence = $start;
    }

    /**
     * Define the model's default state (Customer).
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

        // Relasi ke desa (DS0000001 s/d DS0000020)
        $desaCode = sprintf('DS%07d', (($num - 1) % 20) + 1);

        return [
            'CST'        => sprintf('CST%07d', $num), // 10 chars PK
            'DES'        => substr($cstName, 0, 150),
            'AL'         => substr(fake('id_ID')->address(), 0, 100),
            'TLP'        => sprintf('0812%08d', $num),
            'DS'         => $desaCode, // Kode desa di tabel cst
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State untuk membuat data desa (AL=null, TLP=null, DS=null).
     */
    public function desa(string $namaDesa = null): static
    {
        return $this->state(function (array $attributes) use ($namaDesa) {
            $num = static::$sequence++;
            return [
                'CST'        => sprintf('DS%07d', $num),
                'DES'        => $namaDesa ?? ('Desa ' . fake('id_ID')->city()),
                'AL'         => null,
                'TLP'        => null,
                'DS'         => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });
    }
}
