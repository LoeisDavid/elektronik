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
            'CST' => sprintf('CST%07d', $num), // 10 chars, fits string(10)
            'DES' => substr(fake()->company(), 0, 150),
            'TLP' => substr(fake()->phoneNumber(), 0, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
