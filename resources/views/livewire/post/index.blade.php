<?php

use App\Events\PostCreatedEvent;
use App\Models\Post;
use App\Notifications\Post\PostCreated;
use Illuminate\Support\Facades\Auth;

use function Livewire\Volt\{computed, state};

state(['title']);

$posts = computed(function () {
    return Auth()->user()->posts;
});

$store = function () {
    $post = new Post();
    $post->user_id = auth()->id();
    $post->title = $this->title;
    $post->save();

    // PostCreatedEvent::dispatch($post);

    Auth::user()->notify(new PostCreated($post));

    $this->title = '';
};

$delete = function (string $id) {
    $post = Post::findOrFail($id);
    $this->authorize('delete', $post);
    $post->delete();
}

?>

<div class="flex flex-col gap-5">

    <form wire:submit="store" class="flex flex-col gap-5">

        <div class="flex flex-col">
            <h2 class="text-xl">Mood</h2>

            <div class="flex gap-2">
                <p class="text-neutral-500">How you feeling?</p>
                <span wire:loading><x-loader /></span>
            </div>
        </div>

        <div class="flex flex-col">
            <input type="text" wire:model="title" value="{{old('title')}}" class="py-1 px-2 border rounded" placeholder="Title">
            @error('title') <p class="px-2 text-red-500">{{$message}}</p> @enderror
        </div>

        <button type="submit" class="border rounded py-1 px-2 hover:border-black">Post Now</button>

    </form>


    <div class="flex flex-col gap-2">

        @foreach($this->posts as $post)
        <div wire:key="{{ $post->id }}" class="flex flex-col">
            <p class="text-lg">{{$post->title}}</p>
            <p class="">{{$post->description}}</p>

            <div class="flex gap-2 items-center">
                <p class="text-xs font-thin">{{$post->created_at}}</p>
                <button class="text-xs" wire:click="delete({{$post->id}})" wire:confirm="Are you sure you?">Delete</button>
            </div>
        </div>
        @endforeach

    </div>

    <script>
        /*
        document.addEventListener('alpine:init', () => {
            Echo.private(`posts.created`)
                .listen('PostCreatedEvent', (e) => {
                    console.log('Event Created', e);
                });
            // php artisan queue:work
            // php artisan reverb:start
        })
        */
        /*
        document.addEventListener('alpine:init', () => {

            let userId = {{auth()->id()}};

            Echo.private('App.Models.User.' + userId)
                .notification((notification) => {
                    console.log(notification.title);
                });
        })
        */
    </script>

</div>