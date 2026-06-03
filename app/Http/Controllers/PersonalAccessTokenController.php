<?php

namespace App\Http\Controllers;

use App\Enums\PersonalAccessToken\PersonalAccessTokenAbilityEnum;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use DateTime;
use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
use Jenssegers\Agent\Agent;
use Laravel\Sanctum\NewAccessToken;

class PersonalAccessTokenController extends Controller
{

    public CarbonImmutable $expires_at;
    public Agent $agent;
    // public ?User $user;

    /*
        NewAccessToken { $accessToken, $accessToken }
     */

    public function __construct()
    {
        $this->expires_at = now()->addMonths(1);
        $this->agent = new Agent();
        // $this->user = request()->user();

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


    // protected function createTokenName(User $user)
    // {

    //     $token = $user->createToken($tokenName, ['*'], $tokenExpiresAt);
    //     // $token = $user->createToken($tokenName);

    //     return [
    //         'token' => $token->plainTextToken,
    //         'expires_at' => $tokenExpiresAt,
    //         'token_name' => $tokenName
    //     ];
    // }
}
