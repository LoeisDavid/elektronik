<?php

namespace Database\Factories;

use App\Models\MRK;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MRK>
 */
class MRKFactory extends Factory
{
    protected $model = MRK::class;
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
            'MRK' => sprintf('MRK%07d', $num), // 10 chars, fits string(10)
            'DES' => substr(fake()->unique(true)->word() . ' ' . $num, 0, 45), // fits string(45)
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
