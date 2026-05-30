<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Jenssegers\Agent\Agent;

class AuthController extends Controller
{

    use ResponseTrait;

    protected function createTokenName(User $user)
    {
        $agent = new Agent();

        $device = $agent->device();
        $platform = $agent->platform();
        $browser = $agent->browser();

        $tokenName = "$device - $platform - $browser";

        $tokenExpiresAt = now()->addMonths(1);

        $token = $user->createToken($tokenName, ['*'], $tokenExpiresAt);

        return [
            'token' => $token->plainTextToken,
            'expires_at' => $tokenExpiresAt,
            'token_name' => $tokenName
        ];
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = new User();
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
                ]
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
}
