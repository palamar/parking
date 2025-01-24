<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Metadata\Exception\HttpExceptionInterface;

class BaseException extends Exception implements Responsable, HttpExceptionInterface
{
    private array $responseData = [];
    public function __construct(array $responseData = [])
    {
        parent::__construct();
        $this->responseData = $responseData;
    }

    public function toResponse($request): Response
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        try {
            $response->setContent(json_encode($this->responseData, JSON_THROW_ON_ERROR));
        } catch (Throwable $e) {
            Log::error($e);
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $response;
    }

    public function getStatusCode(): int
    {
        return 0;
    }

    public function getHeaders(): array
    {
        return [];
    }
}
