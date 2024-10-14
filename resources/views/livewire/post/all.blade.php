<?php

use App\Models\Post;

use function Livewire\Volt\{computed, state};

$posts = computed(function () {
    return Post::latest()->paginate(4);
});

state([
    'count' => cache()->flexible('count', [5, 10], function () {
        return Post::count();
    }),
]);

//wire:poll.6s
?>

<div class="flex flex-col gap-5">

    <div class="flex justify-between items-center">
        <h2 class="text-xl">All Posts ({{$this->count}})</h2>
        <a href="{{route('home.post')}}" class="text-sm underline">My Posts</a>
    </div>

    <div class="flex flex-col md:flex-row gap-2 md:gap-5">

        @foreach($this->posts as $post)
        <div wire:key="{{ $post->id }}" class="flex flex-col">
            <p class="">{{$post->title}}</p>
            <p class="text-xs font-thin">{{$post->created_at}}</p>
        </div>
        @endforeach

    </div>

    {{$this->posts->links()}}

</div>