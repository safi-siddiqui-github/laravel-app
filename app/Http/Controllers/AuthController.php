<?php

namespace App\Http\Controllers;

use App\Enums\OtpType;
use App\Models\Otp;
use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;
use Throwable;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    use ResponseTrait;

    public OtpController $otpController;

    public function __construct()
    {
        $this->otpController = new OtpController();
    }

    protected function createTokenName(User $user)
    {
        $agent = new Agent();

        $device = $agent->device();
        $platform = $agent->platform();
        $browser = $agent->browser();

        $tokenName = "$device - $platform - $browser";

        $tokenExpiresAt = now()->addMonths(1);

        $token = $user->createToken($tokenName, ['*'], $tokenExpiresAt);
        // $token = $user->createToken($tokenName);

        return [
            'token' => $token->plainTextToken,
            'expires_at' => $tokenExpiresAt,
            'token_name' => $tokenName
        ];
    }

    public function slugify(string $email): string
    {
        $username = Str::before($email, '@');
        $usernameSlug = Str::slug($username);
        $ulid = Str::ulid();
        $combinedSlug = Str::slug("$usernameSlug-$ulid");

        return $combinedSlug;
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = new User();
        $user->username = $this->slugify($validated['email']);
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->save();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => "Registration failed",
            ]);
        }

        $token = $this->createTokenName($user);
        $tokenString = $token['token'];
        $tokenExpiresAt = $token['expires_at'];
        if (!$token || !$tokenString || !$tokenExpiresAt) {
            throw ValidationException::withMessages([
                'email' => "Registration failed",
            ]);
        }

        $this->otpController->store($request->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpType::EMAIL_VERIFY_OTP->value
        ]));

        return $this->apiResponse(
            message: 'User Registered',
            data: [
                'user' => $user,
                'token' => [
                    'token' => $tokenString,
                    'expires_at' => $tokenExpiresAt
                ]
            ]
        );
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255|exists:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::firstWhere('email', $validated['email']);

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => "Login failed",
            ]);
        }

        $check = Hash::check($validated['password'], $user->password);

        if (!$check) {
            throw ValidationException::withMessages([
                'email' => "Login failed",
            ]);
        }

        $token = $this->createTokenName($user);

        $tokenString = $token['token'];
        $tokenExpiresAt = $token['expires_at'];
        if (!$token || !$tokenString || !$tokenExpiresAt) {
            throw ValidationException::withMessages([
                'email' => "Login failed",
            ]);
        }

        return $this->apiResponse(
            message: 'User Logged In',
            data: [
                'user' => $user,
                'token' => [
                    'token' => $tokenString,
                    'expires_at' => $tokenExpiresAt
                ],
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

        $this->otpController->verify($request->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpType::EMAIL_VERIFY_OTP->value,
            'code' => $validated['code'],
        ]));

        $user->markEmailAsVerified();
        new Verified($user);

        return $this->apiResponse(
            message: 'Email Verified',
            data: [
                'user' => $user,
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

        $this->otpController->store($request->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpType::EMAIL_VERIFY_OTP->value,
            'email' => $user->email,
        ]));

        $this->otpController->expireAll($request->mergeIfMissing([
            'user_id' => $user->id,
            'type' => OtpType::EMAIL_VERIFY_OTP->value,
        ]));

        return $this->apiResponse(
            message: 'Email Verification Resend',
        );
    }
}
