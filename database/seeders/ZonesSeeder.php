<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\Vehicle;

class InitialSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedZones();
    }

    private function seedZones(): void
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
            Zone::factory()->create($zone);
        }
    }

    private function seedVehicles(): void
    {

    }
}
