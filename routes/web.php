<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::name('auth.')->prefix('auth')->middleware('guest')->group(function () {
    Route::view('login', 'auth.login')->name('login');
    Route::view('register', 'auth.register')->name('register');
});

Route::name('home.')->group(function () {
    Route::view('/', 'home.index')->name('index');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::view('posts', 'home.post')->name('post');
    });
});

Route::prefix('email/verify')->name('verification.')->middleware('auth')->group(function () {

    Route::get('/', function () {
        session()->flash('info', 'Verify you email !');
        return to_route('home.index');
    })->name('notice');

    Route::get('/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        session()->flash('status', 'Email Verified');
        return to_route('home.index');
    })->name('verify')->middleware('signed');
});

Route::prefix('password')->name('password.')->middleware('guest')->group(function () {

    Route::view('/forgot', 'auth.forgot-password')->name('request');

    Route::get('/reset/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('reset');
    
});
