<?php

namespace App\Enums\PersonalAccessToken;

enum PersonalAccessTokenAbilityEnum: string
{
    case ALLOW_ALL = '*';
    case ALLOW_EMAIL_VERIFICATION = 'ALLOW_EMAIL_VERIFICATION';
    case ALLOW_PASSWORD_RESET = 'ALLOW_PASSWORD_RESET';
}
