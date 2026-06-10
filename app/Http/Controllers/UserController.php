<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use ResponseTrait;

    public function slugUsingEmail(string $email): string
    {
        $username = Str::before($email, '@');
        $usernameSlug = Str::slug($username);
        $ulid = Str::ulid();
        $combinedSlug = Str::slug("$usernameSlug-$ulid");

        return $combinedSlug;
    }

    public function storeUsingEmailPassword(): User
    {
        request()->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'name' => 'sometimes|string|min:5|max:100',
        ]);

        $email = request()->input('email');

        $user = new User();
        $user->username = $this->slugUsingEmail($email);
        $user->email = $email;
        $user->password = request()->input('password');
        $user->name = request()->input('name');
        $user->save();

        // event(new Registered($user));

        return $user;
    }

    public function verifyUsingEmailPassword(): User
    {
        request()->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8',
        ]);

        $email = request()->input('email');

        $user = User::where('email', $email)->first();

        $check = Hash::check(request()->input('password'), $user->password);

        if (!$check) {
            throw ValidationException::withMessages([
                'email' => "Login failed",
            ]);
        }

        return $user;
    }

    public function upsertUsingSocialLogin(): User
    {
        request()->validate([
            'email' => 'required|email',
            'email_verified' => 'required|boolean',
            'name' => 'required|string|min:1|max:100',
            'avatar' => 'sometimes|string|min:1|max:100',
            'google_id' => [
                'sometimes',
                'string',
                'required_without:github_id,microsoft_id',
                'prohibits:github_id,microsoft_id',
            ],
            'github_id' => [
                'sometimes',
                'string',
                'required_without:google_id,microsoft_id',
                'prohibits:google_id,microsoft_id',
            ],
            'microsoft_id' => [
                'sometimes',
                'string',
                'required_without:google_id,github_id',
                'prohibits:google_id,github_id',
            ],
        ]);

        $email = request()->input('email');
        // $newUser = false;

        $user = User::firstWhere('email', $email);
        $googleId = request()->input('google_id');
        $githubId = request()->input('github_id');
        $microsoftId = request()->input('microsoft_id');
        $emailVerified = request()->boolean('email_verified');
        $avatar = request()->input('avatar');

        if (!$user) {
            // $newUser = true;

            $user = new User();
            $user->email = $email;
            $user->username = $this->slugUsingEmail($email);
            $user->password = Str::uuid();
            $user->name = request()->input('name');
        }

        if ($emailVerified && !$user->isEmailVerified()) {
            $user->email_verified_at = now();
        }

        if ($avatar && !$user->avatar) {
            $user->avatar = $avatar;
        }

        if ($googleId) {
            $user->google_id = $googleId;
        } else if ($githubId) {
            $user->github_id = $githubId;
        } else {
            $user->microsoft_id = $microsoftId;
        }

        $user->save();

        return $user;
    }

    public function verifyUsingEmail(): User
    {
        request()->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $email = request()->input('email');

        $user = User::where('email', $email)->first();

        return $user;
    }

    public function updatePasswordUsingEmail(): User
    {
        request()->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', request()->input('email'))->first();
        $user->password = request()->input('password');
        $user->save();

        return $user;
    }
}
