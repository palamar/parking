<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\ParkingPay;
use App\Exceptions\MissedDataException;
use App\Models\Payment as PaymentModel;
use App\Models\Zone as ZoneModel;
use App\Models\Price as PriceModel;
use App\Models\Vehicle as VehicleModel;
use Illuminate\Support\Facades\Log;
use App\Models\PriceType;

final class ParkingPayProcessor implements ProcessorInterface
{
    /**
     * @param ParkingPay $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return mixed
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        try {
            $vehicleModel = VehicleModel::getOrCreatePrivateVehicle($data->plate);
            $zoneModel = ZoneModel::getByCode($data->zone);
            $priceModel = PriceModel::getActualPriceForTheZone($zoneModel, $data->priceType);
            $startTime = new \DateTimeImmutable();

            $paymentModel = PaymentModel::where(['vehicle_id' => $vehicleModel->id])
                ->where(['zone_id' => $zoneModel->id])
                ->where(['price_id' => $priceModel->id])
                ->where('start_time', '<=', $startTime->format('Y-m-d H:i:s'))
                ->where('end_time', '>=', $startTime->format('Y-m-d H:i:s'))
                ->first();

            if ($paymentModel === null) {
                if ($data->priceType === PriceType::HOUR_TYPE) {
                    $endTime = $startTime->add(new \DateInterval('PT1H'));
                } elseif ($data->priceType === PriceType::DAY_TYPE) {
                    $endTime = $startTime->setTime(23, 59, 59);
                } else {
                    throw new MissedDataException([]);
                }
                $paymentModel = PaymentModel::factory()->create([
                    'vehicle_id' => $vehicleModel->id,
                    'price_id' => $priceModel->id,
                    'zone_id' => $zoneModel->id,
                    'amount' => $priceModel->amount,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'note' => $data->note,
                ]);
            }

            return [
                'id' => $paymentModel->id,
                'zone' => $zoneModel->code,
                'plate' => $vehicleModel->plate,
                'amount' => $priceModel->amount,
                'priceType' => $data->priceType,
                'endTime' => $paymentModel->endTime,
            ];
        } catch (\Throwable $exception) {
            Log::critical(
                "Can't process payment for the parking {$data->plate}.",
                [
                    'exception' => $exception,
                ],
            );
            throw new MissedDataException([
                'plate' => $data->plate,
                'zone' => $data->zone,
                'priceType' => $data->priceType,
                'note' => $data->note,
            ]);
        }
    }
}
