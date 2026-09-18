<?php

namespace Database\Factories;

use App\Models\GD;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GD>
 */
class GDFactory extends Factory
{
    protected $model = GD::class;
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
            'GD' => sprintf('GD%08d', $num), // 10 chars, fits string(10)
            'DES' => substr('Gudang ' . fake()->city() . ' ' . $num, 0, 45), // fits string(45)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
