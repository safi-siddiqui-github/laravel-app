<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel App</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body>
    <header class="flex flex-row p-5 justify-between items-center shadow">

        <nav class="flex">
            <a href="{{route('home.index')}}" class="text-lg">Laravel App</a>
        </nav>

        <nav class="flex gap-2 text-sm">
            @auth
            <p class="">Welcome {{auth()->user()->name}} !</p>
            <livewire:auth.logout />
            @endauth
            @guest
            <a href="{{route('auth.login')}}" class="border rounded py-1 px-2 hover:border-black">Login</a>
            <a href="{{route('auth.register')}}" class="border rounded py-1 px-2 hover:border-black">Register</a>
            @endguest
        </nav>

    </header>

    <x-message />

    <main class="">
        {{$slot}}
    </main>

</body>

</html>