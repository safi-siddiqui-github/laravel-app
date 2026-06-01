<?php

namespace App\Models;

use App\Enums\OtpType;
use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected function casts(): array
    {
        return [
            'type' => OtpType::class,
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }
}
