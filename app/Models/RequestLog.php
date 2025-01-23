<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestLog extends Model
{
    use HasFactory;

    public const SCAN_ACTION = 'scan';
}
