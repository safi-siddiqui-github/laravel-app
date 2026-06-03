<?php

namespace App\Models;

use App\Enums\Otp\OtpTypeEnum;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected function casts(): array
    {
        return [
            'type' => OtpTypeEnum::class,
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }
}
