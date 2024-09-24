<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel App</title>
    @vite('resources/css/app.css')
    @livewireStyles

</head>

<body>
    <header class="flex flex-col sm:flex-row p-5 justify-between items-center shadow">

        <nav class="flex">
            <a href="{{route('home.index')}}" class="text-lg">Laravel App</a>
        </nav>

        <nav class="flex gap-2 ">
            <a href="{{route('post.index')}}" class="{{request()->routeIs('post.index') ? 'font-medium' : ''}}">Posts</a>
        </nav>

        <nav class="flex gap-2 text-sm">
            @auth
            <p class="">Welcome {{auth()->user()->name}} !</p>

            <form action="{{route('auth.logout_post')}}" method="post">
                @csrf
                <button type="submit" class="hover:underline text-neutral-500">Logout</button>
            </form>
            @endauth
            @guest
            <a href="{{route('auth.login')}}" class="border rounded py-1 px-2 hover:border-black">Login</a>
            <a href="{{route('auth.register')}}" class="border rounded py-1 px-2 hover:border-black">Register</a>
            @endguest
        </nav>

    </header>

    <main class="">
        {{$slot}}
    </main>

    @livewireScripts
</body>

</html>