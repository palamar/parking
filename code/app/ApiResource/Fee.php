<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\RequestBody;
use ApiPlatform\OpenApi\Model\Response;
use App\Exceptions\WrongZoneException;
use App\State\FeeProcessor;
use App\State\FeeProvider;
use App\State\FeePaymentProcessor;

#[Post(
    uriTemplate: '/fees',
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
                                    'plate' => [
                                        'type' => 'string',
                                    ],
                                    'zone' => [
                                        'type' => 'string',
                                    ],
                                    'amount' => [
                                        'type' => 'float',
                                    ],
                                    'priceType' => [
                                        'type' => 'string',
                                    ],
                                    'endTime' => [
                                        'type' => 'string',
                                    ],
                                    'note' => [
                                        'type' => 'string',
                                    ],
                                ],
                            ],
                            'example' => [
                                'id' => 1,
                                'zone' => 'A',
                                'plate' => 'QW 123',
                                'amount' => 123.34,
                                'priceType' => 'hour',
                                'entTime' => '2025-01-20T22:41:27.362Z',
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
                                    'plate' => [
                                        'type' => 'string',
                                    ],
                                    'zone' => [
                                        'type' => 'string',
                                    ],
                                    'priceType' => [
                                        'type' => 'string',
                                    ],
                                    'note' => [
                                        'type' => 'string',
                                    ]
                                ],
                            ],
                            'example' => [
                                'zone' => 'A',
                                'plate' => 'QW 123',
                                'priceType' => 'hour',
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
                                'plate' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'description' => 'plate of the car',
                                ],
                                'zone' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'description' => 'parking zone',
                                ],
                                'scannerCode' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'description' => 'code of the device',
                                ],
                                'operatorCode' => [
                                    'type' => 'string',
                                    'required' => true,
                                    'description' => 'code of the device',
                                ],
                            ],
                        ],
                        'example' => [
                            'plate' => 'QW 123',
                            'zone' => 'A',
                            'scannerCode' => 'SC1',
                            'operatorCode' => 'OP1',
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
    processor: FeeProcessor::class,
)]
class Fee
{
    public function __construct(
        public string $zone,
        public string $plate,
        public string $scannerCode,
        public string $operatorCode,
        public ?string $note,
    ) {
        $this->zone =  mb_strtoupper(mb_trim((string) $zone));
        $this->plate = mb_strtoupper(mb_trim((string) $plate));
        $this->scannerCode = mb_strtoupper(mb_trim((string) $scannerCode));
        $this->operatorCode = mb_strtoupper(mb_trim((string) $this->operatorCode));
        $this->note = mb_trim((string) $note);
    }
}
