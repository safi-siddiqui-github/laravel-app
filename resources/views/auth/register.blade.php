<x-auth_layout>

    <form method="post" action="{{route('auth.register_post')}}" class="flex flex-col gap-5 px-5 pt-10 sm:w-96 sm:mx-auto text-sm">
        @csrf

        <div class="flex flex-col gap-1">
            <h2 class="text-xl">Register</h2>
            <p class="text-neutral-500">Existing User! <a href="{{route('auth.login')}}" class="hover:underline">Login Now</a> </p>
        </div>

        <div class="flex flex-col gap-2">

            <div class="flex flex-col">
                <input type="text" name="name" value="{{old('name')}}" class="py-1 px-2 border rounded" placeholder="Name">
                @error('name') <p class="px-2 text-red-500">{{$message}}</p> @enderror
            </div>

            <div class="flex flex-col">
                <input type="email" name="email" value="{{old('email')}}" class="py-1 px-2 border rounded" placeholder="Email">
                @error('email') <p class="px-2 text-red-500">{{$message}}</p> @enderror
            </div>

            <div class="flex flex-col">
                <input type="text" name="username" value="{{old('username')}}" class="py-1 px-2 border rounded" placeholder="Username">
                @error('username') <p class="px-2 text-red-500">{{$message}}</p> @enderror
            </div>

            <div class="flex flex-col">
                <input type="password" name="password" class="py-1 px-2 border rounded" placeholder="Password">
                @error('password') <p class="px-2 text-red-500">{{$message}}</p> @enderror
            </div>
        </div>

        <button type="submit" class="border rounded py-1 px-2 hover:border-black">Register</button>

    </form>

</x-auth_layout>