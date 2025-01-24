<?php

declare(strict_types=1);

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use App\ApiResource\ParkingPay;
use App\Models\OwnershipType as OwnershipTypeModel;
use App\Models\Vehicle as VehicleModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ApiResource()]
class Vehicle extends Model
{
    use HasFactory;

    public static function getOrCreatePrivateVehicle(string $plate): VehicleModel
    {
        $plate = mb_strtoupper($plate);
        $vehicle = VehicleModel::where(['plate' => $plate])->first();
        if ($vehicle === null) {
            $ownershipType = OwnershipTypeModel::where(['code' => OwnershipTypeModel::PRIVATE])->first();
            $vehicle = VehicleModel::factory()->create([
                'plate' => $plate,
                'ownership_type_id' => $ownershipType->id,
            ]);
        }

        return $vehicle;
    }
}
