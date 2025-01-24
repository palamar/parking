<?php

declare(strict_types=1);

namespace App\Models;

use ApiPlatform\Metadata\ApiResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

#[ApiResource()]
class Operator extends Model
{
    use HasFactory;

    /**
     * @param string $code
     * @return static
     * @throws ModelNotFoundException
     */
    public static function getByCode(string $code): static
    {
        $code = mb_trim($code);
        return static::where(['operator_code' => $code])->firstOrFail();
    }
}
