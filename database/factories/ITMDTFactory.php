<?php

namespace Database\Factories;

use App\Models\GD;
use App\Models\ITM;
use App\Models\ITMDT;
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
            'HRB' => fake()->randomFloat(2, 5000, 40000000),
            'GD' => fn () => GD::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
