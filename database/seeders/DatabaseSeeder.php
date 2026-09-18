<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['USERNAME' => 'admin'],
            [
                'PASS' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'AL' => 'Jl. Elektronik No. 1',
                'HP' => '08123456789',
                'DES' => 'Administrator Toko',
                'ROLE' => 1,
            ]
        );
    }
}
