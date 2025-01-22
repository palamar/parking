<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use ApiPlatform\Metadata\GetCollection;

#[GetCollection(
    paginationEnabled: false,
)]
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

    /**
     * Mark fee for the calculation.
     */
    public const FEE_TYPE = 'fee';

    public static function getByType(string $type): ?PriceType
    {
        return static::where(['code' => $type])->first();
    }
}
