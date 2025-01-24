<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Get;
use App\Exceptions\MissedDataException;
use App\State\ParkingCheckProvider;
use App\Exceptions\ParkingForbiddenException;
use App\Exceptions\WrongZoneException;
use ApiPlatform\Metadata\QueryParameter;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Models\RequestLog;

#[Get(
    uriTemplate: '/parking/check',
    openapi: new Operation(
        responses: [
            '200' => new Response(
                description: 'Not Paid',
                content: new \ArrayObject(
                    [
                        'application/ld+json' => [
                            'schema' => [
                                'properties' => [
                                    'zone' => [
                                        'type' => 'string',
                                    ],
                                    'plate' => [
                                        'type' => 'string',
                                    ],
                                    'paidToDateTime' => [
                                        'type' => 'string',
                                    ],
                                ],
                            ],
                            'example' => [
                                'zone' => 'A',
                                'plate' => 'QW 123',
                                'paidToDateTime' => '2025-01-22T14:33:27.026Z',
                            ],
                        ],
                    ]
                ),
            ),
            '404' => new Response(
                description: 'Not Paid, fee has to bee created',
                content: new \ArrayObject(
                    [
                        'application/json' => [
                            'schema' => [
                                'properties' => [
                                    'zone' => [
                                        'type' => 'string',
                                    ],
                                    'plate' => [
                                        'type' => 'string',
                                    ],
                                    'amount' => [
                                        'type' => 'float',
                                    ],
                                ],
                            ],
                            'example' => [
                                'zone' => 'A',
                                'plate' => 'QW 123',
                                'amount' => 123.34,
                            ],
                        ],
                    ]
                ),
            ),
            '410' => new Response(
                description: 'Zone is not configured',
                content: new \ArrayObject(
                    [
                        'application/json' => [
                            'schema' => [
                                'properties' => [
                                    'plate' => [
                                        'type' => 'string',
                                    ],
                                    'zone' => [
                                        'type' => 'string',
                                    ],
                                ],
                            ],
                            'example' => [
                                'zone' => 'A',
                                'plate' => 'QW 123',
                            ],
                        ],
                    ]
                ),
            ),
        ],
    ),
    exceptionToStatus: [
        ParkingForbiddenException::class => 404,
        WrongZoneException::class => 410,
        MissedDataException::class => 410,
    ],
    shortName: 'Parking',
    provider: ParkingCheckProvider::class,
)]
#[QueryParameter(key: ParkingCheck::ZONE, property: ParkingCheck::ZONE)]
#[QueryParameter(key: ParkingCheck::PLATE, property: ParkingCheck::PLATE)]
#[QueryParameter(key: 'operator_code')]
#[QueryParameter(key: 'scanner_code')]
class ParkingCheck
{
    public const ZONE = 'zone';
    public const PLATE = 'plate';

    public function __construct(
        public string $zone,
        public string $plate,
        public string $paidToDateTime,
    ) {
    }
}
