<x-auth_layout>

    <form method="post" action="{{route('auth.login_post')}}" class="flex flex-col gap-5 px-5 pt-10 sm:w-96 sm:mx-auto text-sm">
        @csrf

        <div class="flex flex-col gap-1">
            <h2 class="text-xl">Login</h2>
            <p class="text-neutral-500">New User! <a href="{{route('auth.register')}}" class="hover:underline">Register Now</a> </p>
        </div>

        <div class="flex flex-col gap-2">

            <div class="flex flex-col">
                <input type="email" name="email" value="{{old('email')}}" class="py-1 px-2 border rounded" placeholder="Email">
                @error('email') <p class="px-2 text-red-500">{{$message}}</p> @enderror
            </div>

            <div class="flex flex-col">
                <input type="password" name="password" class="py-1 px-2 border rounded" placeholder="Password">
                @error('password') <p class="px-2 text-red-500">{{$message}}</p> @enderror
            </div>

            <div class="flex gap-2 items-center justify-end">
                <label for="remember" class="text-neutral-500">Remember Me</label>
                <input id="remember" type="checkbox" name="remember" checked class="size-4">
                @error('remember') <p class="px-2 text-red-500">{{$message}}</p> @enderror
            </div>
        </div>

        <button type="submit" class="border rounded py-1 px-2 hover:border-black">Login</button>

    </form>

</x-auth_layout>