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
    <nav class="p-5">
        <a href="/" class="{{request()->is('/') ? 'text-blue-500' : ''}}">Home</a>
        <a href="/about" class="{{request()->is('about') ? 'text-blue-500' : ''}}">About</a>
        <a href="/jobs" class="{{request()->is('jobs') ? 'text-blue-500' : ''}}">Jobs</a>
        <a href="/posts" class="{{request()->is('posts') ? 'text-blue-500' : ''}}">posts</a>
    </nav>

    {{$slot}}
    @livewireScripts
</body>

</html>