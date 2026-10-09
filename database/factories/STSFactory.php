<?php

namespace Database\Factories;

use App\Models\ACR;
use App\Models\ITM;
use App\Models\ITMDT;
use App\Models\STS;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<STS>
 */
class STSFactory extends Factory
{
    protected $model = STS::class;
    protected static int $sequence = 0;

    public static function resetSequence(int $start = 0): void
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
        // Base36 encoding ensures 10,000 unique values fit strictly within string(3)
        $recKey = str_pad(base_convert((string)$num, 10, 36), 3, '0', STR_PAD_LEFT);

        return [
            'REC' => $recKey, // string(3) PK
            'REF_NO' => fn () => ACR::factory(),
            'BC_OUT' => function (array $attributes) {
                if (!empty($attributes['ITM'])) {
                    return ITMDT::factory()->create(['ITM' => $attributes['ITM']])->BC_OUT;
                }
                return ITMDT::factory()->create()->BC_OUT;
            },
            'ITM' => function (array $attributes) {
                if (!empty($attributes['BC_OUT'])) {
                    $dt = ITMDT::where('BC_OUT', $attributes['BC_OUT'])->first();
                    if ($dt) {
                        return $dt->ITM;
                    }
                }
                return ITM::factory();
            },
            'HGJ' => fake()->randomFloat(2, 10000, 50000000),
            'ST' => fake()->randomElement(['PCS', 'BOX', 'UNT']), // 3 chars
            'POT_1' => fake()->optional(0.3)->numberBetween(1, 10),
            'POT_2' => fake()->optional(0.2)->numberBetween(1, 5),
            'POT_R' => fake()->optional(0.2)->randomFloat(2, 5000, 100000),
            'PPN' => fake()->optional(0.5)->randomElement([11, 12]),
            'BRT' => fake()->randomFloat(2, 10000, 50000000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (STS $sts) {
            $totalDt = ITMDT::where('ITM', $sts->ITM)->count();
            $totalSts = STS::where('ITM', $sts->ITM)->count();
            ITM::where('ITM', $sts->ITM)->update(['STOK' => max(0, $totalDt - $totalSts)]);
        });
    }
}
