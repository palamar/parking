<?php

namespace App\Exceptions;

use ApiPlatform\Metadata\ErrorResource;

#[ErrorResource]
class ParkingForbiddenException extends BaseException
{
}
