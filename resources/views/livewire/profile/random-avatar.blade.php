<?php

use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{state};

state(['random_image']);

$random = function () {
    $this->random_image = "https://picsum.photos/id/" . mt_rand(1, 1000) . "/300/300";
};

$setAvatar = function () {

    $this->validate([
        'random_image' => 'required|url'
    ]);

    $user = Auth::user();
    $user->avatar = $this->random_image;
    $user->save();
};

?>

<div class="flex flex-col gap-2 items-start">

    <p class="text-lg font-medium">Select random image</p>

    <div class="flex gap-2">
        <button wire:click="random" class="text-sm bg-blue-500 px-2 py-1 text-white rounded">Random Image</button>
        <button wire:click="setAvatar" class="text-sm bg-black px-2 py-1 text-white rounded">Set as profile</button>
    </div>
    @error($random_image) <p class="text-red-500 text-sm">{{$message}}</p> @enderror
    @if($random_image)
    <img src="{{$random_image}}" alt="generated image" class="rounded">
    @endif
</div>