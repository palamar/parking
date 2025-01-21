<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Scanner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class ScannersSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $scanners = [
            [
                'scanner_code' => 'SC1',
                'public_key' => 'public_key1',
            ],
            [
                'scanner_code' => 'SC2',
                'public_key' => 'public_key2',
            ],
        ];

        foreach ($scanners as $scanner) {
            try {
                Scanner::factory()->create($scanner);
            } catch (\Throwable) {
                Log::error("Failed to create scanner with zone: " . $scanner['scanner_code']);
            }
        }
    }
}
