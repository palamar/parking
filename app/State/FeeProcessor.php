<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Exceptions\MissedDataException;
use App\Models\Price as PriceModel;
use App\Models\PriceType;
use App\Models\Vehicle as VehicleModel;
use App\Models\Zone as ZoneModel;
use App\Models\Scanner as ScannerModel;
use App\Models\Operator as OperatorModel;
use App\ApiResource\Fee as FeeResource;
use App\Models\Fee as FeeModel;
use Illuminate\Support\Facades\Log;


final class FeeProcessor implements ProcessorInterface
{
    /**
     * @param FeeResource $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return array
     * @throws MissedDataException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        try {
            $vehicleModel = VehicleModel::getOrCreatePrivateVehicle($data->plate);
            $zoneModel = ZoneModel::getByCode($data->zone);
            $feeModelForToday = FeeModel::getFeeForTheVehicleForToday($zoneModel, $vehicleModel);
            // vehicle already has fee in one of the zones with the same or higher ranks.
            if ($feeModelForToday !== null) {
                return [
                    'id' => $feeModelForToday->id,
                    'zone' => $zoneModel->code,
                    'plate' => $vehicleModel->plate,
                    'amount' => $feeModelForToday->amount,
                    'priceType' => PriceType::FEE_TYPE,
                    'entTime' => $feeModelForToday->due_date,
                    'note' => $feeModelForToday->note,
                ];
            }
            $priceModel = PriceModel::getActualFeeForTheZone($zoneModel);
            $scannerModel = ScannerModel::getByCode($data->scannerCode);
            $operatorModel = OperatorModel::getByCode($data->operatorCode);
            $dateTime = (new \DateTime())->setTime(23, 59, 59);
            $newFeeModel = FeeModel::factory()->create([
                'vehicle_id' => $vehicleModel->id,
                'price_id' => $priceModel->id,
                'scanner_id' => $scannerModel->id,
                'operator_id' => $operatorModel->id,
                'zone_id' => $zoneModel->id,
                'amount' => $priceModel->amount,
                'issue_date' => $dateTime->format('Y-m-d'),
                'issue_time' => $dateTime->format('H:i:s'),
                'due_date' => $dateTime->format('Y-m-d H:i:s'),
                'note' => $data->note,
            ]);
            return [
                'id' => $newFeeModel->id,
                'zone' => $zoneModel->code,
                'plate' => $vehicleModel->plate,
                'amount' => $newFeeModel->amount,
                'priceType' => PriceType::FEE_TYPE,
                'entTime' => $newFeeModel->due_date,
                'note' => $newFeeModel->note,
            ];
        } catch (\Throwable $e) {
            $data = [
                'zone' => $zoneModel->code,
                'plate' => $vehicleModel->plate,
                'scannerCode' => $data->scannerCode,
                'operatorCode' => $data->operatorCode,
                'note' => $data->note,
            ];
            Log::critical(
                'Can\'t save fee.',
                [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'data' => $data,
                ]
            );

            throw new MissedDataException($data);
        }
    }
}
