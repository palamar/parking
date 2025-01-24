<?php

namespace App\Exceptions;

use ApiPlatform\Metadata\ErrorResource;

#[ErrorResource]
class WrongZoneException extends BaseException
{
}
