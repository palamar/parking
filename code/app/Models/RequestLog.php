<?php

declare(strict_types=1);

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ApiResource()]
class RequestLog extends Model
{
    use HasFactory;

    public const SCAN_ACTION = 'scan';
}
