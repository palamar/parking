<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Price;
use App\Models\PriceType;
use App\Models\Zone;
use Database\Seeders\PriceTypesSeeder;
use Database\Seeders\ZonesSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Log;

class PricesSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PriceTypesSeeder::class,
            ZonesSeeder::class,
        ]);

        $zoneA = Zone::where(['code' => 'A'])->first();
        $zoneB = Zone::where(['code' => 'B'])->first();
        $zoneC = Zone::where(['code' => 'C'])->first();
        $zoneD = Zone::where(['code' => 'D'])->first();

        $hourPrice = PriceType::where(['code' => PriceType::HOUR_TYPE])->first();
        $dayPrice = PriceType::where(['code' => PriceType::DAY_TYPE])->first();

        $vehicles = [
            [
                'zone_id' => $zoneA->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 123,
            ],
            [
                'zone_id' => $zoneA->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 1230,
            ],
            [
                'zone_id' => $zoneB->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 100,
            ],
            [
                'zone_id' => $zoneB->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 1000,
            ],
            [
                'zone_id' => $zoneC->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 80,
            ],
            [
                'zone_id' => $zoneC->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 800,
            ],
            [
                'zone_id' => $zoneD->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 60,
            ],
            [
                'zone_id' => $zoneD->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('c'),
                'active_to' => null,
                'amount' => 600,
            ],
        ];

        foreach ($vehicles as $zone) {
            Price::factory()->create($zone);
        }
    }
}
