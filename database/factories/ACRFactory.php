<?php

namespace Database\Factories;

use App\Models\ACR;
use App\Models\CST;
use App\Models\SLS;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ACR>
 */
class ACRFactory extends Factory
{
    protected $model = ACR::class;
    protected static int $sequence = 1;

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
        $brt = fake()->randomFloat(2, 50000, 250000000);
        $pot1 = fake()->optional(0.3)->numberBetween(1, 10);
        $pot2 = fake()->optional(0.2)->numberBetween(1, 5);
        $potR = fake()->optional(0.2)->randomFloat(2, 5000, 100000);
        $totPot = ($potR ?? 0) + ($brt * (($pot1 ?? 0) / 100));
        $pay = max(0, $brt - $totPot);

        return [
            'REF_NO' => sprintf('INV%07d', $num), // 10 chars, fits string(10) PK
            'DT' => fake()->date(),
            'CST' => fn () => CST::factory(),
            'SLS' => fn () => SLS::factory(),
            'BRT' => $brt,
            'PAY' => $pay,
            'TOT_POT' => $totPot,
            'JNS' => fake()->randomElement([1, 2]),
            'POT_1' => $pot1,
            'POT_2' => $pot2,
            'POT_R' => $potR,
            'USER' => sprintf('usr%07d', (($num - 1) % 10000) + 1), // fits string(10)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
