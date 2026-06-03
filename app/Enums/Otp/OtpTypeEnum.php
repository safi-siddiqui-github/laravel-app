<?php

namespace App\Enums\Otp;

enum OtpTypeEnum: string
{
    case EMAIL_VERIFY_OTP = 'EMAIL_VERIFY_OTP';
    case PHONE_VERIFY_OTP = 'PHONE_VERIFY_OTP';
    case PASSWORD_RESET_OTP = 'PASSWORD_RESET_OTP';
    case PASSWORD_RESET_SESSION = 'PASSWORD_RESET_SESSION';
}
