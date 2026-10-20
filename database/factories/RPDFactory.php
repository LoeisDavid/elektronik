<?php

namespace Database\Factories;

use App\Models\ITMDT;
use App\Models\RP;
use App\Models\RPD;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RPD>
 */
class RPDFactory extends Factory
{
    protected $model = RPD::class;
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
        $code = 'RPD' . date('ymd') . str_pad($num, 4, '0', STR_PAD_LEFT);

        return [
            'RPD'        => $code,
            'RP'         => RP::factory(),
            'BC_OUT'     => fn () => ITMDT::factory()->create()->BC_OUT,
            'SS'         => 'Ready',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function ready(): static
    {
        return $this->state(fn () => ['SS' => 'Ready']);
    }

    public function dikirim(): static
    {
        return $this->state(fn () => ['SS' => 'Dikirim']);
    }

    public function diterima(): static
    {
        return $this->state(fn () => ['SS' => 'Diterima']);
    }

    public function rusak(): static
    {
        return $this->state(fn () => ['SS' => 'Rusak']);
    }

    public function hilang(): static
    {
        return $this->state(fn () => ['SS' => 'Hilang']);
    }

    public function ditolak(): static
    {
        return $this->state(fn () => ['SS' => 'Ditolak']);
    }
}
