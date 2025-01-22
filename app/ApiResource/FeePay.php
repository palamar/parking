<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use App\Exceptions\WrongZoneException;
use App\State\FeePaymentProcessor;

#[Post(
    uriTemplate: '/fees/{id}/pay',
    openapi: new Operation(
        responses: [
            '201' => new Response(
                description: 'Parking is covered',
                content: new \ArrayObject(
                    [
                        'application/json' => [
                            'schema' => [
                                'properties' => [
                                    'id' => [
                                        'type' => 'integer',
                                    ],
                                    'feeId' => [
                                        'type' => 'integer',
                                    ],
                                    'amount' => [
                                        'type' => 'float',
                                    ],
                                    'paymentDetails' => [
                                        'type' => 'string',
                                    ],
                                    'note' => [
                                        'type' => 'string',
                                    ],
                                ],
                            ],
                            'example' => [
                                'id' => 1,
                                'feeId' => 2,
                                'amount' => 123.34,
                                'paymentDetails' => 'some note',
                                'note' => 'some note',
                            ],
                        ],
                    ]
                ),
            ),
            '410' => new Response(
                description: 'Gone',
                content: new \ArrayObject(
                    [
                        'application/ld+json' => [
                            'schema' => [
                                'properties' => [
                                    'feeId' => [
                                        'type' => 'integer',
                                    ],
                                    'amount' => [
                                        'type' => 'float',
                                    ],
                                    'paymentDetails' => [
                                        'type' => 'string',
                                    ],
                                    'note' => [
                                        'type' => 'string',
                                    ],
                                ],
                            ],
                            'example' => [
                                'feeId' => 2,
                                'amount' => 123.34,
                                'paymentDetails' => 'some note',
                                'note' => 'some note',
                            ],
                        ],
                    ],
                ),
            ),
        ],
        requestBody: new RequestBody(
            content: new \ArrayObject(
                [
                    'application/ld+json' => [
                        'schema' => [
                            'properties' => [
                                'feeId' => [
                                    'type' => 'integer',
                                    'required' => true,
                                    'description' => 'fee id',
                                ],
                                'amount' => [
                                    'type' => 'float',
                                    'required' => true,
                                    'description' => 'amount of the payment',
                                ],
                                'paymentDetails' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'description' => 'data from the bank',
                                ],
                                'note' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'description' => 'some note',
                                ],
                            ],
                        ],
                        'example' => [
                            'feeId' => 1,
                            'amount' => 2.45,
                            'paymentDetails' => 'bank data',
                            'note' => 'some note',
                        ],
                    ],
                ]
            ),
        ),
    ),
    exceptionToStatus: [
        WrongZoneException::class => 410,
    ],
    shortName: 'Fee',
    processor: FeePaymentProcessor::class,
)]
class FeePay
{
    public function __construct(
        public int $feeId,
        public float $amount,
        public ?string $paymentDetails,
        public ?string $note,
    ) {
        $this->feeId = (int) $feeId;
        $this->amount = (float) $amount;
        $this->paymentDetails = (string) $paymentDetails;
        $this->note = (string) $note;
    }
}
