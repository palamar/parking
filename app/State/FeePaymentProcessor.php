<?php

declare(strict_types=1);

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\FeePay;
use App\Exceptions\MissedDataException;
use App\Models\Fee as FeeModel;
use App\Models\FeePayment as FeePaymentModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

final class FeePaymentProcessor implements ProcessorInterface
{
    /**
     * @param FeePay $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return array
     * @throws MissedDataException
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): array
    {
        try {
            $feePaymentModel = FeePaymentModel::where(['fee_id' => $data->feeId])->first();
            if ($feePaymentModel !== null) {
                return [
                    'id' => $feePaymentModel->id,
                    'feeId' => $feePaymentModel->fee_id,
                    'amount' => $feePaymentModel->amount,
                    'paymentDetails' => $feePaymentModel->paymentDetails,
                    'note' => $feePaymentModel->note,
                ];
            }
            try {
                $feeModel = FeeModel::where(['id' => $data->feeId])->firstOrFail();
                DB::beginTransaction();
                if ($feeModel->amount != $data->amount) {
                    Log::critical(
                        'Fee payment and fee have different amount.',
                        [
                            'data' => $data,
                        ]
                    );
                    throw new MissedDataException($data);
                }
                $feePaymentModel = FeePaymentModel::factory()->create([
                    'fee_id' => $data->feeId,
                    'amount' => $data->amount,
                    'payment_details' => $data->paymentDetails,
                    'note' => $data->note,
                ]);
                $feeModel->is_closed = 1;
                $feeModel->save();
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::critical(
                    'Can\'t save fee payment.',
                    [
                        'data' => $data,
                        'trace' => $e->getTrace(),
                        'message' => $e->getMessage(),
                    ]
                );
                throw new MissedDataException($data);
            }

            return [
                'id' => $feePaymentModel->id,
                'fee_id' => $feePaymentModel->fee_id,
                'amount' => $feePaymentModel->amount,
                'payment_details' => $feePaymentModel->paymentDetails,
                'note' => $feePaymentModel->note,
            ];
        } catch (\Throwable $e) {
            Log::critical(
                'Can\'t save fee payment.',
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
