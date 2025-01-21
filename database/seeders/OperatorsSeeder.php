<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Operator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class OperatorsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $vehicles = [
            [
                'operator_code' => 'OP1',
                'name' => 'Name1',
                'surname' => 'Surname1',
                'public_key' => 'public_key1',
            ],
            [
                'operator_code' => 'OP2',
                'name' => 'Name1',
                'surname' => 'Surname1',
                'public_key' => 'public_key1',
            ],
        ];

        foreach ($vehicles as $zone) {
            try {
                Operator::factory()->create($zone);
            } catch (\Throwable) {
                Log::error(sprintf('Failed to create vehicle: %s', $zone['operator_code']));
            }
        }
    }
}
