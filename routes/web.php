<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::name('home.')->controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('index');
});

Route::name('post.')->controller(PostController::class)->middleware('auth')->group(function () {
    Route::get('/posts', 'index')->name('index');
    Route::post('/posts', 'store')->name('store');
    Route::delete('/posts/{id}', 'delete')->name('delete');
});

Route::name('auth.')->prefix('auth')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login')->middleware('guest');
    Route::post('/login', 'login_post')->name('login_post')->middleware('guest');
    Route::get('/register', 'register')->name('register')->middleware('guest');
    Route::post('/register', 'register_post')->name('register_post')->middleware('guest');
    Route::post('/logout', 'logout_post')->name('logout_post')->middleware('auth');
});
