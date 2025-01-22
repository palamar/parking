<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Exceptions\MissedDataException;
use App\Models\Payment;
use App\Models\Vehicle;
use App\Models\Vehicle as VehicleModel;
use App\Models\Zone as ZoneModel;
use App\Models\Fee as FeeModel;
use App\Models\Price as PriceModel;
use App\ApiResource\ParkingCheck;
use App\Exceptions\ParkingForbiddenException;
use App\Exceptions\WrongZoneException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

final class ParkingCheckProvider implements ProviderInterface
{
    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return object|array|object[]|null
     * @throws ParkingForbiddenException
     * @throws WrongZoneException
     * @throws MissedDataException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $parameters = $operation->getParameters();
        $plate =  mb_strtoupper(mb_trim((string) $parameters->get(ParkingCheck::PLATE)?->getValue()));
        $zone = mb_strtoupper(mb_trim((string) $parameters->get(ParkingCheck::ZONE)?->getValue()));
        $zoneModel = $this->getZoneModelOrException($zone);
        $vehicleModel = $this->getVehicleModelOrException($plate, $zoneModel);
        $feeModelForZone = $this->getFeeForTheVehicleForToday($zoneModel, $vehicleModel);

        // if vehicle already has fee, in the zone with the same of lover rank, it's allowed to park here
        if ($feeModelForZone !== null) {
            $endTodayTime = new \DateTime();
            $endTodayTime->setTime(23, 59, 59);

            return new ParkingCheck(
                zone: $zoneModel->code,
                plate: $vehicleModel->plate,
                paidToDateTime: $endTodayTime->format('Y-m-d H:i:s'),
            );
        }

        $paymentModel = $this->getPaymentModelOrException($vehicleModel, $zoneModel);

        return new ParkingCheck(
            zone: $zoneModel->code,
            plate: $vehicleModel->plate,
            paidToDateTime: $paymentModel->end_time,
        );
    }

    private function getFeeForTheVehicleForToday(ZoneModel $zoneModel, Vehicle $vehicleModel): ?FeeModel
    {
        $feeModels = FeeModel::where(['vehicle_id' => $vehicleModel->id])
            ->where(['issue_date' => (new \DateTimeImmutable())->format('Y-m-d H:i:s')])
            ->get();
        foreach ($feeModels as $feeModel) {
            $feeZoneModel = ZoneModel::get($feeModel->zone_id);
            // vehicle already has fee for the parking with more ranking zone, it's allowed to park in this zone
            // till the end of the day
            if ($zoneModel->rank <= $feeZoneModel->rank) {
                return $feeModel;
            }
        }

        return null;
    }

    /**
     * @param string $zone
     * @return ZoneModel
     * @throws WrongZoneException
     */
    private function getZoneModelOrException(string $zone): ZoneModel
    {
        $zoneModel = ZoneModel::getByCode($zone);
        // some unregistered zone, just in case.
        if ($zoneModel === null) {
            throw new WrongZoneException([
                'zone' => $zone,
                'error' => 'Zone not found',
            ]);
        }
        return $zoneModel;
    }

    /**
     * @param string $plate
     * @param ZoneModel $zoneModel
     * @return Vehicle
     * @throws MissedDataException
     * @throws ParkingForbiddenException
     * @throws MissedDataException
     */
    private function getVehicleModelOrException(string $plate, ZoneModel $zoneModel): Vehicle
    {
        $vehicleModel = VehicleModel::where(['plate' => $plate])->first();
        // vehicle isn't registered, so parking isn't covered, we can notify operator that they can proceed with a fee.
        if ($vehicleModel === null) {
            try {
                $priceFeeModel = PriceModel::getActualFeeForTheZone($zoneModel);
            } catch (ModelNotFoundException) {
                Log::critical("We don't have fee price for the Zone: {$zoneModel->code}");
                // we can't make fee, as we didn't configured data for it.
                throw new MissedDataException([]);
            }

            throw new ParkingForbiddenException([
                'plate' => $plate,
                'zone' => $zoneModel->code,
                'amount' => $priceFeeModel->amount,
            ]);
        }

        return $vehicleModel;
    }

    /**
     * @param VehicleModel $vehicleModel
     * @param ZoneModel $zoneModel
     * @return Payment
     * @throws ParkingForbiddenException
     */
    private function getPaymentModelOrException(VehicleModel $vehicleModel, ZoneModel $zoneModel): Payment
    {
        $paymentModel = Payment::where('vehicle_id', '=', $vehicleModel->id)
            ->where('zone_id', '=', $zoneModel->id)
            ->where('start_time', '<=', (new \DateTimeImmutable())->format('Y-m-d H:i:s'))
            ->where('end_time', '>=', (new \DateTimeImmutable())->format('Y-m-d H:i:s'))
            ->first();
        // there isn't payment for the vehicle and zone
        if ($paymentModel === null) {
            $feePriceModel = PriceModel::getActualFeeForTheZone($zoneModel);
            throw new ParkingForbiddenException([
                'plate' => $vehicleModel->plate,
                'zone' => $zoneModel->code,
                'amount' => $feePriceModel->amount,
                'note' => 'you can generate fee for the vehicle',
            ]);
        }

        return $paymentModel;
    }
}
