<?php

namespace App\Http\Controllers;

use App\Enums\Otp\OtpTypeEnum;
use App\Http\Resources\PersonalAccessToken\PersonalAccessTokenResource;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

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
        $user = $this->userController->storeUsingEmailPassword();
        $token = $this->personalAccessTokenController->storeAllowEmailVerification($user);

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::EMAIL_VERIFY_OTP->value,
        ]);
        $this->otpController->store();

        $tokenResource = new PersonalAccessTokenResource(
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

        $tokenResource = new PersonalAccessTokenResource(
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
        $user = $request->user();

        if ($user->email_verified_at) {
            throw ValidationException::withMessages([
                'email' => "Email already verified",
            ]);
        }

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::EMAIL_VERIFY_OTP->value,
        ]);
        $this->otpController->verify();

        $user->markEmailAsVerified();
        new Verified($user);

        $token = $this->personalAccessTokenController->storeAllowAll($user);

        $tokenResource = new PersonalAccessTokenResource(
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

    public function socialLogin(Request $request)
    {
        $user = $this->userController->upsertUsingSocialLogin();

        $token = null;

        if ($user->isEmailVerified()) {
            $token =  $this->personalAccessTokenController->storeAllowAll($user);
        } else {
            $token =  $this->personalAccessTokenController->storeAllowEmailVerification($user);
        }

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Social Login Success',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function forgotPasswordRequest(Request $request)
    {
        \App\Models\Otp::truncate();

        $user = $this->userController->verifyUsingEmail();

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::PASSWORD_RESET_OTP->value,
        ]);
        $this->otpController->store();

        return $this->apiResponse(
            message: 'Forgot Password Reqeust Success',
        );
    }

    public function forgotPasswordVerify(Request $request)
    {
        $user = $this->userController->verifyUsingEmail();
        $user->can_reset_password = true;

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::PASSWORD_RESET_OTP->value,
        ]);
        $this->otpController->verify();

        $token =  $this->personalAccessTokenController->storeAllowForgotPassword($user);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Forgot Password Verify Success',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }

    public function forgotPasswordResend(Request $request)
    {
        $user = $this->userController->verifyUsingEmail();

        request()->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpTypeEnum::PASSWORD_RESET_OTP->value,
            'email' => $user->email,
        ]);

        $otp = $this->otpController->store();

        request()->mergeIfMissing([
            'otp_id' => $otp->id,
        ]);
        $this->otpController->expireAll();

        return $this->apiResponse(
            message: 'Forgot Password Resend Success',
        );
    }

    public function forgotPasswordReset(Request $request)
    {
        $user = request()->user();

        request()->mergeIfMissing([
            'email' => $user->email,
        ]);

        $user = $this->userController->updatePasswordUsingEmail();

        $token =  $this->personalAccessTokenController->storeAllowAll($user);

        $tokenResource = new PersonalAccessTokenResource(
            token: $token
        );

        return $this->apiResponse(
            message: 'Forgot Password Reset Success',
            data: [
                'user' => $user->toResource(),
                'token' => $tokenResource,
            ]
        );
    }
}
