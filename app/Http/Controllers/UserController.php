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

    public function currentUser(Request $request)
    {
        return $this->apiResponse(
            message: 'Current User',
            data: [
                'user' => $request->user()->toResource(),
            ]
        );
    }

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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
