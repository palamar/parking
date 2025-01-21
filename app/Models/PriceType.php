<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PriceType extends Model
{
    use HasFactory;

    /**
     * Mark day based price for calculation.
     */
    public const DAY_TYPE = 'day';

    /**
     * Mark hour based price for calculation.
     */
    public const HOUR_TYPE = 'hour';
}
