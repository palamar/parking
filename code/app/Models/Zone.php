<?php

declare(strict_types=1);

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ApiResource()]
class Zone extends Model
{
    use HasFactory;

    public static function getByCode(string $code): ?self
    {
        return static::where(['code' => $code])->first();
    }
}
