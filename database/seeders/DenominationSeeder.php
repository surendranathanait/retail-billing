<?php

namespace Database\Seeders;

use App\Models\Denomination;
use Illuminate\Database\Seeder;

class DenominationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $denominations = [
            ['value' => 2000, 'available_count' => 50],
            ['value' => 500, 'available_count' => 100],
            ['value' => 200, 'available_count' => 23],
            ['value' => 100, 'available_count' => 123],
            ['value' => 50, 'available_count' => 10],
            ['value' => 20, 'available_count' => 50],
            ['value' => 10, 'available_count' => 100],
            ['value' => 5, 'available_count' => 200],
            ['value' => 2, 'available_count' => 300],
            ['value' => 1, 'available_count' => 500],
        ];

        foreach ($denominations as $denomination) {
            Denomination::create($denomination);
        }
    }
}
