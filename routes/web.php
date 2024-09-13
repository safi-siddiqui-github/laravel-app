<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('home');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/jobs', function () {
    return view('jobs');
});

Route::get('/jobs/{id}', function () {
    return view('job');
});

Route::controller(PostController::class)->group(function () {
    Route::get('/posts', 'index');
});
