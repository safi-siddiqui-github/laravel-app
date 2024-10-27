<?php

use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{computed, state};

state(['prefers_sms' => auth()->user()->prefers_sms ? true : false]);

$notifications = computed(function () {
    return auth()->user()->notifications;
});

$change = function () {
    $user = Auth::user();
    $user->prefers_sms = $this->prefers_sms;
    $user->save();
};

$markAsRead = function ($notification) {
    $user = Auth::user();
    $notif = $user->notifications->find($notification['id']);
    $notif->markAsRead();
};

$markAsUnRead = function ($notification) {
    $user = Auth::user();
    $notif = $user->notifications->find($notification['id']);
    $notif->markAsUnRead();
};

$delete = function ($notification) {
    $user = Auth::user();
    $notif = $user->notifications->find($notification['id']);
    $notif->delete();
};

?>

<div class="flex flex-col gap-5">

    <div class="flex flex-col gap-2">
        <p class="text-xl">Notifications</p>
        <form wire:submit="change" class="flex gap-2 items-center">
            <label for="prefersSms" class="">Prefer SMS</label>
            <input id="prefersSms" type="checkbox" wire:model="prefers_sms" class="size-4">
            <button class="border rounded text-sm px-2 py-1" type="submit">Save</button>
        </form>
    </div>

    <div class="flex flex-col gap-1 text-sm">
        @foreach($this->notifications as $each)
        <div class="flex items-center gap-2">
            <p>{{$each->data['title']}} @if(!$each->read_at) (new) @endif</p>
            @if($each->read_at)
            <button class="bg-red-500 text-white px-2 py-1 rounded" wire:click="markAsUnRead({{$each}})">Mark as Unread</button>
            @else
            <button class="bg-orange-500 text-white px-2 py-1 rounded" wire:click="markAsRead({{$each}})">Mark as read</button>
            @endif
            <button class="bg-black text-white px-2 py-1 rounded" wire:click="delete({{$each}})">Delete</button>
        </div>
        @endforeach
    </div>
</div>