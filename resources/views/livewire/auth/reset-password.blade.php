<?php

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

use function Livewire\Volt\{rules, state};

state(['email' => request()->query('email'), 'password', 'password_confirmation', 'token']);
rules([
    'email' => 'required|email|exists:users',
    'password' => 'required|string|min:5|confirmed',
]);

$resetPassword = function () {
    $this->validate();

    $status = Password::reset(
        [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'token' => $this->token,
        ],
        function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        session()->flash('status', 'Password reseted');
    } else {
        session()->flash('info', 'Password reset failed');
    }

    return to_route('auth.login');
};

?>

<form wire:submit.prevent="resetPassword" class="flex flex-col gap-5 px-5 pt-10 sm:w-96 sm:mx-auto text-sm">

    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
            <h2 class="text-xl">Reset Password</h2>
            <span wire:loading>
                <x-loader />
            </span>
        </div>

        <p class="text-neutral-500">Forgot password, No worry</p>
    </div>

    <div class="flex flex-col gap-2">
        <div class="flex flex-col">
            <input type="email" wire:model="email" class="py-1 px-2 border rounded" placeholder="Email">
            @error('email') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>
        <div class="flex flex-col">
            <input type="password" wire:model="password" class="py-1 px-2 border rounded" placeholder="Password">
            @error('password') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>
        <div class="flex flex-col">
            <input type="password" wire:model="password_confirmation" class="py-1 px-2 border rounded" placeholder="Password">
            @error('password') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>
    </div>

    <button type="submit" class="border rounded py-1 px-2 hover:border-black">Send Link</button>

</form>