<?php

namespace Database\Factories;

use App\Models\ITM;
use App\Models\KTG;
use App\Models\MRK;
use App\Models\SPP;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ITM>
 */
class ITMFactory extends Factory
{
    protected $model = ITM::class;
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
        return [
            'ITM' => sprintf('ITM%07d', $num), // 10 chars, fits string(10)
            'DES' => substr(fake()->words(3, true) . ' ' . $num, 0, 150),
            'STOK' => fake()->numberBetween(0, 1000),
            'HRG' => fake()->randomFloat(2, 10000, 50000000),
            'KTG' => fn () => KTG::factory(),
            'MRK' => fn () => MRK::factory(),
            'SPP' => fn () => SPP::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
