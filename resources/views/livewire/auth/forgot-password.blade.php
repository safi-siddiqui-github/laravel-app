<?php

use Illuminate\Support\Facades\Password;

use function Livewire\Volt\{rules, state};

state(['email']);
rules([
    'email' => 'required|email|exists:users'
]);

$forgotPassword = function () {
    $this->validate();

    $status = Password::sendResetLink(['email' => $this->email]);

    if ($status === Password::RESET_LINK_SENT) {
        session()->flash('status', 'Password reset link sent');
    } else {
        session()->flash('info', 'Password reset failed');
    }

    return to_route('auth.login');
};

?>

<form wire:submit.prevent="forgotPassword" class="flex flex-col gap-5 px-5 pt-10 sm:w-96 sm:mx-auto text-sm">

    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
            <h2 class="text-xl">Password Reset Link</h2>
            <span wire:loading>
                <x-loader />
            </span>
        </div>

        <p class="text-neutral-500">Forgot password, No worry</p>
    </div>

    <div class="flex flex-col">
        <input type="email" wire:model="email" class="py-1 px-2 border rounded" placeholder="Email">
        @error('email') <p class="px-2 text-red-500">{{$message}}</p> @enderror
    </div>

    <button type="submit" class="border rounded py-1 px-2 hover:border-black">Send Link</button>

</form>