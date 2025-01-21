<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OwnershipType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class OwnershipTypesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $types = [
            [
                'code' => 'private',
            ],
            [
                'code' => 'municipal',
            ]
        ];

        foreach ($types as $type) {
            try {
            OwnershipType::factory()->create($type);
            } catch (\Throwable) {
                Log::error("Failed to create owner type: {$type['code']}");
            }
        }
    }
}
