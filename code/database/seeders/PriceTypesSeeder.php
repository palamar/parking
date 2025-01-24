<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PriceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class PriceTypesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'code' => PriceType::DAY_TYPE,
            ],
            [
                'code' => PriceType::HOUR_TYPE,
            ],
            [
                'code' => PriceType::FEE_TYPE,
            ]
        ];

        foreach ($vehicles as $zone) {
            try {
                PriceType::factory()->create($zone);
            } catch (\Exception $e) {
                Log::error('Cannot create price type: ' . $e->getMessage());
            }
        }
    }
}
