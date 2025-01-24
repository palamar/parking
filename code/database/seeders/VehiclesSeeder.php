<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\OwnershipType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class VehiclesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OwnershipTypesSeeder::class,
        ]);

        $privateType = OwnershipType::where(['code' => 'private'])->first();
        $municipalType = OwnershipType::where(['code' => 'municipal'])->first();

        $vehicles = [
            [
                'plate' => 'A 123',
                'region' => 'USA',
                'ownership_type_id' => $privateType->id,
            ],
            [
                'plate' => 'B 345',
                'region' => 'CA',
                'ownership_type_id' => $municipalType->id,
            ],
        ];

        foreach ($vehicles as $zone) {
            try {
                Vehicle::factory()->create($zone);
            } catch (\Throwable) {
                Log::error("Vehicle creation failed with message: {$zone['plate']}");
            }
        }
    }
}
