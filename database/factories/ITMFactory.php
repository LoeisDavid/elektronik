<?php

namespace Database\Factories;

use App\Models\ITM;
use App\Models\KTG;
use App\Models\MRK;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ITM>
 */
class ITMFactory extends Factory
{
    protected $model = ITM::class;
    protected static int $sequence = 1;

    protected static array $electronicProducts = [
        'Kipas Angin Stand Fan 16 Inch',
        'TV LED 32 Inch Smart TV',
        'Mesin Cuci Front Loading 8kg',
        'Kulkas 2 Pintu Inverter',
        'AC Split 1 PK Low Watt',
        'Rice Cooker Digital 1.8L',
        'Blender Multi-Function 2L',
        'Smartphone 8GB/128GB',
        'Laptop Core i5 16GB',
        'Speaker Bluetooth Portable',
        'Set Top Box TV Digital',
        'Setrika Listrik Anti Lengket',
        'Powerbank 20000mAh',
        'Headset Wireless Noise Cancelling',
        'Microwave Oven 20L',
        'Dispenser Air Galon Bawah',
        'Air Purifier HEPA Filter',
        'Soundbar 2.1 Subwoofer',
        'Tablet 10 Inch LTE',
        'Smartwatch Sport GPS',
        'Router Wi-Fi Dual Band',
        'Kamera CCTV Wireless 1080p',
        'Air Fryer Digital 4L',
        'Vacuum Cleaner Cordless',
        'Pompa Air Otomatis',
        'Water Heater Listrik 15L',
        'Mixer Adonan Roti 5L',
        'Juicer Slow Extractor',
        'Toaster Pemanggang Roti 2 Slices',
        'Monitor Gaming 24 Inch 144Hz',
        'Keyboard Mechanical RGB',
        'Mouse Wireless Ergonomic',
        'Harddisk Eksternal 1TB',
        'Smart Bulb Lampu LED WiFi',
        'Stopkontak Smart Plug WiFi',
        'Kompor Induksi Portabel',
        'Exhaust Fan Dinding 10 Inch',
        'Chopper Bumbu Daging',
        'Rice Cooker Mini 0.6L',
        'Kipas Angin Dinding 18 Inch',
        'Android TV Box 4K',
        'Projector Portable HD',
        'Smart Door Lock Fingerprint',
        'Walkie Talkie Dual Band',
        'Stabilizer Voltase 1000VA',
        'UPS Power Supply 600VA',
        'Earphones TWS True Wireless',
        'Web Camera HD 1080p Autofocus',
        'Electric Kettle Teko Listrik 1.5L',
        'Deep Fryer Listrik 3L',
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
        $idx = ($num - 1) % count(static::$electronicProducts);
        $prodName = static::$electronicProducts[$idx];
        if ($num > count(static::$electronicProducts)) {
            $prodName .= ' Tipe ' . sprintf('%03d', $num);
        }

        return [
            'ITM' => sprintf('ITM%07d', $num), // 10 chars, fits string(10)
            'DES' => substr($prodName, 0, 150),
            'STOK' => 0, // Disesuaikan dengan jumlah ITMDT pada Seeder
            'HRG' => fake()->numberBetween(150, 15000) * 1000,
            'KTG' => fn () => KTG::factory(),
            'MRK' => fn () => MRK::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ITM $itm) {
            $totalDt = \App\Models\ITMDT::where('ITM', $itm->ITM)->count();
            if ($totalDt > 0) {
                $totalSts = \App\Models\STS::where('ITM', $itm->ITM)->count();
                $itm->update(['STOK' => max(0, $totalDt - $totalSts)]);
            }
        });
    }

    /**
     * State untuk barang dengan stok habis (STOK = 0).
     */
    public function habis(): static
    {
        return $this->state(fn (array $attributes) => [
            'STOK' => 0,
        ]);
    }

    /**
     * State untuk barang dengan stok menipis (1..5).
     */
    public function menipis(int $stok = 3): static
    {
        return $this->state(fn (array $attributes) => [
            'STOK' => max(1, min(5, $stok)),
        ]);
    }

    /**
     * State untuk barang dengan stok banyak (> 5).
     */
    public function banyak(int $stok = 15): static
    {
        return $this->state(fn (array $attributes) => [
            'STOK' => max(6, $stok),
        ]);
    }

    /**
     * Membuat barang lengkap dengan relasi ITMDT dan STS,
     * sehingga STOK = jumlah ITMDT - jumlah STS.
     */
    public function withStockDetails(int $inStock = 5, int $sold = 0): static
    {
        return $this->afterCreating(function (ITM $itm) use ($inStock, $sold) {
            $totalDt = $inStock + $sold;
            for ($i = 0; $i < $totalDt; $i++) {
                $dt = \App\Models\ITMDT::factory()->create([
                    'ITM' => $itm->ITM,
                ]);

                if ($i < $sold) {
                    \App\Models\STS::factory()->create([
                        'ITM'    => $itm->ITM,
                        'BC_OUT' => $dt->BC_OUT,
                    ]);
                }
            }

            $itm->update(['STOK' => $inStock]);
        });
    }
}

