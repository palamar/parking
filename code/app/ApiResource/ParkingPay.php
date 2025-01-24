<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\Post;
use App\State\ParkingPayProcessor;
use App\Exceptions\ParkingForbiddenException;
use App\Exceptions\WrongZoneException;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\OpenApi\Model\RequestBody;

#[Post(
    uriTemplate: '/parking',
    openapi: new Operation(
        responses: [
            '200' => new Response(
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
                                    'priceType' => [
                                        'type' => 'integer',
                                    ],
                                    'amount' => [
                                        'type' => 'float',
                                    ],
                                    'note' => [
                                        'type' => 'string',
                                    ],
                                ],
                            ],
                            'example' => [
                                'id' => 12,
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
                                    'priceType' => [
                                        'type' => 'integer',
                                    ],
                                    'amount' => [
                                        'type' => 'float',
                                    ]
                                ],
                            ],
                            'example' => [
                                'id' => 12,
                                'zone' => 'A',
                                'plate' => 'QW 123',
                                'amount' => 123.34,
                                'priceType' => 'hour',
                                'endTime' => '2025-01-20T22:41:27.362Z',
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
                                'priceType' => [
                                    'type' => 'integer',
                                    'required' => true,
                                    'description' => 'price type, eg. hour|day',
                                ],
                            ],
                        ],
                        'example' => [
                            'plate' => 'QW 123',
                            'zone' => 'A',
                            'priceType' => 'hour',
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
    shortName: 'Parking',
    processor: ParkingPayProcessor::class,
)]
class ParkingPay
{
    public function __construct(
        public string $zone,
        public string $plate,
        public string $priceType,
        public ?string $note = null,
    ) {
        $this->zone =  mb_strtoupper(mb_trim((string) $zone));
        $this->plate = mb_strtoupper(mb_trim((string) $plate));
        $this->priceType = mb_strtolower(mb_trim((string) $priceType));
        $this->note = mb_trim((string) $note);
    }
}
