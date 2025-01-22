<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use ApiPlatform\Metadata\GetCollection;

#[GetCollection()]
class Zone extends Model
{
    use HasFactory;

    public static function getByCode(string $code): ?self
    {
        return static::where(['code' => $code])->first();
    }
}
