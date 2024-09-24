<x-layout>
    <div class="flex flex-col p-5 gap-5">

        <h2 class="text-xl">Posts</h2>

        <div class="grid grid-cols-3 gap-5">
            @foreach($posts->items() as $post)
            <div class="p-2 border rounded flex flex-col gap-5">
                <p class="text-sm">{{$post->description}}</p>

                <div class="flex justify-between">
                    <p class="text-xl">{{$post->title}}</p>
                    <p class="text-sm">By {{$post->user->name}}</p>
                </div>

                <div class="flex justify-between">
                    @foreach($post->tags as $tag)
                    <p class="text-sm font-semibold">#{{$tag->name}}</p>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        {{$posts->links()}}
    </div>

</x-layout>