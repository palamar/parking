<?php

namespace App\Exceptions;

use ApiPlatform\Metadata\ErrorResource;

#[ErrorResource]
class MissedDataException extends BaseException
{
}
