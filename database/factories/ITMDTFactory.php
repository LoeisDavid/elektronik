<?php

namespace Database\Factories;

use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\SPP;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ITMDT>
 */
class ITMDTFactory extends Factory
{
    protected $model = ITMDT::class;
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
            'ITM' => fn () => ITM::factory(),
            'BC_IN' => sprintf('BCI%017d', $num), // 20 chars, fits string(20)
            'BC_OUT' => sprintf('BCO%017d', $num), // 20 chars, fits string(20) PK
            'TM' => fake()->date(),
            'HRB' => fake()->numberBetween(100, 12000) * 1000,
            'GD' => fn () => GD::factory(),
            'SPP' => fn () => SPP::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (ITMDT $itmdt) {
            $totalDt = ITMDT::where('ITM', $itmdt->ITM)->count();
            $totalSts = \App\Models\STS::where('ITM', $itmdt->ITM)->count();
            ITM::where('ITM', $itmdt->ITM)->update(['STOK' => max(0, $totalDt - $totalSts)]);
        });
    }
}

