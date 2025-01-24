<?php

declare(strict_types=1);

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use App\Models\Fee as FeeModel;
use App\Models\Zone as ZoneModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ApiResource()]
class Fee extends Model
{
    use HasFactory;

    /**
     * @param Zone $zoneModel
     * @param Vehicle $vehicleModel
     * @return ?static
     */
    public static function getFeeForTheVehicleForToday(ZoneModel $zoneModel, Vehicle $vehicleModel): ?static
    {
        $feeModels = static::where(['vehicle_id' => $vehicleModel->id])
            ->where(['issue_date' => (new \DateTimeImmutable())->format('Y-m-d')])
            ->get();

        foreach ($feeModels as $feeModel) {
            $feeZoneModel = ZoneModel::where(['id' => $feeModel->zone_id])->first();
            // vehicle already has fee for the parking with more ranking zone, it's allowed to park in this zone
            // till the end of the day
            if ($zoneModel->rank <= $feeZoneModel->rank) {
                return $feeModel;
            }
        }

        return null;
    }
}
