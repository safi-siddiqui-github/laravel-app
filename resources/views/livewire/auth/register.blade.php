<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;

use function Livewire\Volt\{rules, state, action};

state(['name', 'email', 'username', 'password']);
rules([
    'name' => ['required', 'string', 'max:100'],
    'email' => ['required', 'string', 'email', 'unique:users', 'max:100'],
    'username' => ['required', 'string', 'unique:users', 'max:100'],
    'password' => ['required', 'string', 'min:5', 'max:100'],
]);


$verifyEvent = action(fn($user) => event(new Registered($user)))->renderless();

$save = function () {
    $this->validate();

    $user = new User();
    $user->name = $this->name;
    $user->email = $this->email;
    $user->username = $this->username;
    $user->password = $this->password;
    $user->save();

    $this->verifyEvent($user);

    session()->flash('status', 'Account created ');

    $this->redirectRoute('auth.login');
}

?>

<form wire:submit.prevent="save" class="flex flex-col gap-5 px-5 pt-10 sm:w-96 sm:mx-auto text-sm">

    <div class="flex flex-col gap-1">
        <div class="flex items-center gap-2">
            <h2 class="text-xl">Register</h2>
            <span wire:loading>
                <x-loader />
            </span>
        </div>

        <p class="text-neutral-500">Existing User! <a href="{{route('auth.login')}}" class="hover:underline">Login Now</a> </p>
    </div>

    <div class="flex flex-col gap-2">

        <div class="flex flex-col">
            <input type="text" wire:model="name" class="py-1 px-2 border rounded" placeholder="Name">
            @error('name') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>

        <div class="flex flex-col">
            <input type="email" wire:model="email" class="py-1 px-2 border rounded" placeholder="Email">
            @error('email') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>

        <div class="flex flex-col">
            <input type="text" wire:model="username" class="py-1 px-2 border rounded" placeholder="Username">
            @error('username') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>

        <div class="flex flex-col">
            <input type="password" wire:model="password" class="py-1 px-2 border rounded" placeholder="Password">
            @error('password') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>
    </div>

    <button type="submit" class="border rounded py-1 px-2 hover:border-black">Register</button>

</form>