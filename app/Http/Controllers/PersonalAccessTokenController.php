<?php

namespace App\Http\Controllers;

use App\Enums\PersonalAccessToken\PersonalAccessTokenAbilityEnum;
use App\Models\User;
use Carbon\CarbonImmutable;
use Jenssegers\Agent\Agent;
use Laravel\Sanctum\NewAccessToken;

class PersonalAccessTokenController extends Controller
{

    public CarbonImmutable $expires_at;
    public Agent $agent;

    public function __construct()
    {
        $this->expires_at = now()->addMonths(1);
        $this->agent = new Agent();

        // /** @var User $user */
    }

    public function name(): string
    {
        $device = $this->agent->device();
        $platform = $this->agent->platform();
        $browser = $this->agent->browser();

        return "$device - $platform - $browser";
    }

    public function storeAllowAll(User $user): NewAccessToken
    {
        return $user->createToken(
            name: $this->name(),
            abilities: [PersonalAccessTokenAbilityEnum::ALLOW_ALL->value],
            expiresAt: $this->expires_at,
        );
    }

    public function storeAllowEmailVerification(User $user): NewAccessToken
    {
        return $user->createToken(
            name: $this->name(),
            abilities: [PersonalAccessTokenAbilityEnum::ALLOW_EMAIL_VERIFICATION->value],
            expiresAt: now()->addMinutes(10),
        );
    }
    
    public function storeAllowForgotPassword(User $user): NewAccessToken
    {
        return $user->createToken(
            name: $this->name(),
            abilities: [PersonalAccessTokenAbilityEnum::ALLOW_PASSWORD_RESET->value],
            expiresAt: now()->addMinutes(10),
        );
    }
}
