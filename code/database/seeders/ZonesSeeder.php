<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class ZonesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $zones = [
            [
                'code' => 'A',
                'rank' => 1,
            ],
            [
                'code' => 'B',
                'rank' => 2,
            ],
            [
                'code' => 'C',
                'rank' => 3,
            ],
            [
                'code' => 'D',
                'rank' => 4,
            ],
        ];

        foreach ($zones as $zone) {
            try {
                Zone::factory()->create($zone);
            } catch(\Throwable) {
                Log::error("Failed to create zone: {$zone['code']}");
            }
        }
    }
}
