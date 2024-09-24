<x-layout>
    <div class="flex flex-col gap-5 px-5 pt-10 sm:w-96 sm:mx-auto text-sm">

        <form method="post" action="{{route('post.store')}}" class="flex flex-col gap-5">
            @csrf

            <div class="flex flex-col gap-1">
                <h2 class="text-xl">Mood</h2>
                <p class="text-neutral-500">How you feeling?</p>
            </div>

            <div class="flex flex-col gap-2">

                <div class="flex flex-col">
                    <input type="text" name="title" value="{{old('title')}}" class="py-1 px-2 border rounded" placeholder="Title">
                    @error('title') <p class="px-2 text-red-500">{{$message}}</p> @enderror
                </div>

                <div class="flex flex-col">
                    <input type="text" name="description" class="py-1 px-2 border rounded" placeholder="Description">
                    @error('description') <p class="px-2 text-red-500">{{$message}}</p> @enderror
                </div>
            </div>

            <button type="submit" class="border rounded py-1 px-2 hover:border-black">Post Now</button>

        </form>


        <div class="flex flex-col gap-2">

            @foreach($posts as $post)
            <div class="flex flex-col">
                <p class="text-lg">{{$post->title}}</p>
                <p class="">{{$post->description}}</p>

                <div class="flex gap-2 items-center">
                    <p class="text-xs font-thin">{{$post->created_at}}</p>

                    <form action="{{route('post.delete', ['id' => $post->id])}}" method="post" class="">
                        @csrf
                        @method('delete')
                        <button type="submit" class="text-xs">Delete</button>
                    </form>

                </div>
            </div>
            @endforeach
        </div>

        {{$posts->links()}}


    </div>
</x-layout>