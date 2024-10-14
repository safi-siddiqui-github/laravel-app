<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Livewire\Volt\{rules, state};

state(['email', 'password', 'remember' => true]);
rules([
    'email' => ['required', 'string', 'email', 'exists:users', 'max:100'],
    'password' => ['required', 'string', 'min:5', 'max:100'],
    'remember' => ['boolean'],
]);


$login = function () {

    $this->validate();

    if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
        throw ValidationException::withMessages(['password' => 'Incorrect']);
    }

    session()->regenerate();
    session()->flash('status', 'Logged In');
    $this->redirectRoute('home.index');
}

?>

<form wire:submit.prevent="login" class="flex flex-col gap-5 px-5 pt-10 sm:w-96 sm:mx-auto text-sm">

    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
            <h2 class="text-xl">Login</h2>
            <span wire:loading>
                <x-loader />
            </span>
        </div>

        <div class="flex justify-between text-neutral-500">
            <p class="">New User! <a href="{{route('auth.register')}}" class="underline">Register Now</a> </p>

            <a class="underline" href="{{route('password.request')}}">Forgot Password</a>
        </div>
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

        <div class="flex gap-2 items-center justify-end">
            <label for="remember" class="text-neutral-500">Remember Me</label>
            <input id="remember" type="checkbox" wire:model="remember" class="size-4">
            @error('remember') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>
    </div>

    <button type="submit" class="border rounded py-1 px-2 hover:border-black">Login</button>

</form>