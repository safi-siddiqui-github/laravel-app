<?php

use function Livewire\Volt\{state, usesFileUploads};

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

usesFileUploads();

state(['avatar']);

$change = function () {
    $this->validate([
        'avatar' => 'required|image|max:1024'
    ]);

    $user = Auth::user();

    if ($user->avatar) {
        Storage::disk('public')->delete('avatars/' . $user->avatar);
    }

    $name = 'user-avatar-' . Auth::id() . '.' . $this->avatar->getClientOriginalExtension();

    $this->avatar->storeAs(path: 'public/avatars', name: $name);

    $user->avatar = $name;
    $user->save();

    $this->avatar = '';
};

$remove = function () {
    $user = Auth::user();

    if ($user->avatar) {
        Storage::disk('public')->delete('avatars/' . $user->avatar);
        $user->avatar = null;
        $user->save();
    }
};

?>

<div class="flex flex-col gap-2">

    <p class="text-lg font-medium">Upload avatar</p>

    <form wire:submit="change" class="flex flex-col max-w-96 gap-2 items-start">
        <input type="file" wire:model="avatar" class="border rounded p-0.5">
        @error('avatar') <p class="text-red-500 text-sm">{{$message}}</p> @enderror
        <button class="border px-2 py-1 bg-black text-white text-sm rounded " type="submit">Change Avatar</button>
        @if($avatar)
        <img src="{{$avatar->temporaryUrl()}}" alt="" class="max-w-96 rounded">
        @endif
    </form>

    <button wire:click="remove" class="bg-red-500 px-2 py-1 w-fit text-sm text-white rounded">Remove my avatar/image</button>

</div>