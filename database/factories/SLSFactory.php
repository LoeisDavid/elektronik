<?php

namespace Database\Factories;

use App\Models\SLS;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SLS>
 */
class SLSFactory extends Factory
{
    protected $model = SLS::class;
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
            'SLS' => sprintf('SLS%07d', $num), // 10 chars, fits string(10)
            'DES' => substr(fake()->name(), 0, 150),
            'HP' => substr(fake()->phoneNumber(), 0, 20),
            'AL' => substr(fake()->address(), 0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
