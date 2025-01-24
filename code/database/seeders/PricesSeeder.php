<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Price;
use App\Models\PriceType;
use App\Models\Zone;
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
        $feePrice = PriceType::where(['code' => PriceType::FEE_TYPE])->first();

        $vehicles = [
            [
                'zone_id' => $zoneA->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 123.0,
            ],
            [
                'zone_id' => $zoneA->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 1230.0,
            ],
            [
                'zone_id' => $zoneA->id,
                'price_type_id' => $feePrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 2230.0,
            ],
            [
                'zone_id' => $zoneB->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 10.0,
            ],
            [
                'zone_id' => $zoneB->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 100.0,
            ],
            [
                'zone_id' => $zoneB->id,
                'price_type_id' => $feePrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 2230.0,
            ],
            [
                'zone_id' => $zoneC->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 80.0,
            ],
            [
                'zone_id' => $zoneC->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 80.0,
            ],
            [
                'zone_id' => $zoneC->id,
                'price_type_id' => $feePrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 2230.0,
            ],
            [
                'zone_id' => $zoneD->id,
                'price_type_id' => $hourPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 60.0,
            ],
            [
                'zone_id' => $zoneD->id,
                'price_type_id' => $dayPrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 600.0,
            ],
            [
                'zone_id' => $zoneD->id,
                'price_type_id' => $feePrice->id,
                'active_from' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'active_to' => null,
                'amount' => 2230.0,
            ],
        ];

        foreach ($vehicles as $zone) {
            try {
                Price::factory()->create($zone);
            } catch (\Exception $e) {
                Log::error('Cannot create vehicle price: ' . $e->getMessage());
            }
        }
    }
}
