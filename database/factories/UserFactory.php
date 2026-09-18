<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;
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
            'USERNAME' => sprintf('usr%07d', $num), // 10 chars, fits string(20) and acr.USER string(10)
            'PASS' => static::$password ??= Hash::make('password123'),
            'AL' => substr(fake()->address(), 0, 100),
            'HP' => substr(fake()->phoneNumber(), 0, 25),
            'DES' => substr(fake()->name(), 0, 100),
            'ROLE' => fake()->randomElement([1, 2, 3]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
