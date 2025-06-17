<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login()
    {
        return inertia('auth/login');
    }

    public function register()
    {
        return inertia('auth/register');
    }

    public function loginForm(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'remember' => ['boolean'],
        ]);

        if (Auth::attempt($request->only(['email', 'password']), $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('home'));
        }

        throw ValidationException::withMessages([
            'email' => 'Credentials are incorrect',
        ]);
    }

    public function registerForm(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'unique:users,email', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $user = new User();
        $user->email = $request->input('email');
        $user->password = $request->input('password');
        $user->save();

        // Login user
        Auth::login($user, $remember = false);

        // Verifiaction Email
        event(new Registered($user));

        // Verifiaction Notice Requires Auth
        $request->session()->regenerate();

        return redirect()->route('verification.notice');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    public function verificationNotice()
    {
        return inertia('auth/verification-notice');
    }

    public function verificationResend(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $id = $user->id;
        $key = "resend-link:user-$id";

        $attempt = RateLimiter::attempt(
            $key,
            // $perMinute = 1,
            $perTwoMinute = 1,
            function () use ($user) {
                $user->sendEmailVerificationNotification();
            },
            $decaySeconds = 120
        );

        $availableIn = RateLimiter::availableIn($key);

        if (!$attempt) {
            throw ValidationException::withMessages([
                'throttle' => "New email will be sent in $availableIn seconds",
            ]);
        }

        // Verifiaction Link Sent
        throw ValidationException::withMessages([
            'throttle' => 'Email has already been sent.',
        ]);
    }

    public function verificationVerify(EmailVerificationRequest $request)
    {
        $request->fulfill();
        return redirect()->intended(route('home', absolute: false));
    }

    // Social Login
    public function googleRedirect()
    {
        // Cors issue when redirecting
        $redirectUrl = Socialite::driver('google')->redirect()->getTargetUrl();
        return response('', 409)->header('X-Inertia-Location', $redirectUrl);
    }

    public function googleCallback(Request $request)
    {
        $socialUser = Socialite::driver('google')->user();
        return $this->socialLogin($socialUser);
    }

    public function githubRedirect()
    {
        // Cors issue when redirecting
        $redirectUrl = Socialite::driver('github')->redirect()->getTargetUrl();
        return response('', 409)->header('X-Inertia-Location', $redirectUrl);
    }

    public function githubCallback(Request $request)
    {
        $socialUser = Socialite::driver('github')->user();
        return $this->socialLogin($socialUser);
    }

    public function socialLogin($socialUser)
    {
        $newUser = false;

        $username = $socialUser->getName() ?? $socialUser->getNickname();
        $username = Str::of($username)->lower();
        $username = Str::slug($username, '-');
        $email = $socialUser->getEmail();

        $user = User::where('email', $email)->first();
        if (!$user) {
            $newUser = true;
            $user = new User();
            $user->email = $email;
            $user->username = $username;
        }

        $user->avatar = $socialUser->getAvatar();
        $user->name = $socialUser->getName();
        $user->google_id = $socialUser->getId();
        $user->google_token = $socialUser->token;
        $user->save();

        if ($newUser) {
            // Verifiaction Email
            event(new Registered($user));
        }

        Auth::login($user, $remember = true);
        request()->session()->regenerate();

        if ($newUser) {
            return redirect()->route('verification.notice');
        } else {
            return redirect()->route('home');
        }
    }

    public function passwordForgot()
    {
        return inertia('auth/password-forgot');
    }

    public function passwordEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'exists:users,email', 'max:100'],
        ]);

        /** @var \App\Models\User $user */
        $email = $request->input('email');
        $key = "password-email:user-$email";
        $status = null;

        $attempt = RateLimiter::attempt(
            $key,
            // $perMinute = 1,
            $perTwoMinute = 1,
            function () use ($request) {
                $status = Password::sendResetLink($request->only('email'));
                if ($status !== Password::ResetLinkSent) {
                    throw ValidationException::withMessages([
                        'email' => $status,
                    ]);
                }
            },
            $decaySeconds = 120
        );

        $availableIn = RateLimiter::availableIn($key);

        if (!$attempt) {
            throw ValidationException::withMessages([
                'email' => "New email will be sent in $availableIn seconds",
            ]);
        }

        // if attempt true then forward
        return redirect()->route('home');

        // Verifiaction Link Sent
        // throw ValidationException::withMessages([
        //     'email' => 'Email has already been sent.',
        // ]);
    }

    public function passwordReset($token = null)
    {
        $email = request()->query('email');
        return inertia('auth/password-reset', ['token' => $token, 'email' => $email]);
    }

    public function passwordResetForm(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'exists:users,email', 'max:100'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:100'],
        ]);

        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user
                ->forceFill([
                    'password' => Hash::make($password),
                ])
                ->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'email' => $status,
            ]);
        }

        return redirect()->route('login.page');
    }
}
