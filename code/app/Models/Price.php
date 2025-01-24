<?php

declare(strict_types=1);

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[ApiResource()]
class Price extends Model
{
    use HasFactory;

    /**
     * @param Zone $zoneModel
     * @return self
     * @throws ModelNotFoundException
     */
    public static function getActualFeeForTheZone(Zone $zoneModel): self
    {
        return static::getActualPriceForTheZone($zoneModel, PriceType::FEE_TYPE);
    }

    public static function getActualPriceForTheZone(Zone $zoneModel, string $type): static
    {
        $priceTypeModel = PriceType::where(['code' => $type])->firstOrFail();
        $formattedDate = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $where = static::where('zone_id', '=', $zoneModel->id)
            ->where('price_type_id', '=', $priceTypeModel->id)
            ->where('active_from', '<=', $formattedDate)
            ->where(function (Builder $query) use ($formattedDate) {
                return $query->whereNull('active_to')
                    ->orWhere(
                        'active_to',
                        '>=',
                        $formattedDate,
                    );
            });
        return $where->firstOrFail();
    }
}
