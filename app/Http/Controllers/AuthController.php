<?php

namespace App\Http\Controllers;

use App\Enums\Otp\OtpTypeEnum;
use App\Http\Resources\Token\TokenResource;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    use ResponseTrait;

    public PersonalAccessTokenController $personalAccessTokenController;
    public UserController $userController;
    public OtpController $otpController;

    public function __construct()
    {
        $this->personalAccessTokenController = new PersonalAccessTokenController();
        $this->userController = new UserController();
        $this->otpController = new OtpController();
    }

    public function register(Request $request)
    {
        User::truncate();
        Otp::truncate();

        $user = $this->userController->storeUsingEmailPassword();
        $token = $this->personalAccessTokenController->storeAllowEmailVerification($user);

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::EMAIL_VERIFY_OTP->value,
        ]);
        $this->otpController->store();

        $tokenResource = new TokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'User Registered',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource
            ]
        );
    }

    public function login(Request $request)
    {
        $user = $this->userController->verifyUsingEmailPassword();

        $token = null;
        if ($user->isEmailVerified()) {
            $token = $this->personalAccessTokenController->storeAllowAll($user);
        } else {
            $token = $this->personalAccessTokenController->storeAllowEmailVerification($user);
        }

        $tokenResource = new TokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'User Loggedin',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource
            ]
        );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->apiResponse(
            message: 'User Logged Out',
        );
    }

    public function verifyEmail(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();

        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => "Email already verified",
            ]);
        }

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'code' => $validated['code'],
            'type' => OtpTypeEnum::EMAIL_VERIFY_OTP->value,
        ]);
        $this->otpController->verify();

        $user->markEmailAsVerified();
        new Verified($user);

        $token = $this->personalAccessTokenController->storeAllowAll($user);

        $tokenResource = new TokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Email Verified',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => "Email already verified",
            ]);
        }

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::EMAIL_VERIFY_OTP->value,
            'email' => $user->email,
        ]);

        $otp = $this->otpController->store();

        request()->mergeIfMissing([
            'otp_id' => $otp->id,
        ]);
        $this->otpController->expireAll();

        return $this->apiResponse(
            message: 'Email Verification Resend',
        );
    }

    
}
