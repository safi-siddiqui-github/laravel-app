<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('welcome');
})->name('home');

Route::controller(AuthController::class)->group(function () {
    Route::middleware('guest')->group(function () {
        // login
        Route::name('login.')
            ->prefix('login')
            ->group(function () {
                Route::get('/', 'login')->name('page');
                Route::post('/', 'loginForm')->name('form');
            });

        // register
        Route::name('register.')
            ->prefix('register')
            ->group(function () {
                Route::get('/', 'register')->name('page');
                Route::post('/', 'registerForm')->name('form');
            });

        // google
        Route::name('google.')
            ->prefix('google')
            ->group(function () {
                Route::get('/redirect', 'googleRedirect')->name('login');
                Route::get('/callback', 'googleCallback');
            });

        // github
        Route::name('github.')
            ->prefix('github')
            ->group(function () {
                Route::get('/redirect', 'githubRedirect')->name('login');
                Route::get('/callback', 'githubCallback');
            });

        // Password Reset
        Route::name('password.')
            ->prefix('password')
            ->group(function () {
                Route::get('/forgot', 'passwordForgot')->name('request');
                Route::post('/email', 'passwordEmail')->name('email');
                Route::get('/reset/{token}', 'passwordReset')->name('reset');
                Route::post('/reset', 'passwordResetForm')->name('resetForm');
            });
    });

    Route::middleware('auth')->group(function () {
        // Email Verification
        Route::name('verification.')
            ->prefix('verification')
            ->middleware('notVerified')
            ->group(function () {
                Route::get('/notice', 'verificationNotice')->name('notice');
                Route::post('/resend', 'verificationResend')->name('resend');
                Route::get('/email/verify/{id}/{hash}', 'verificationVerify')->name('verify')->middleware('signed');
            });

        // Logout
        Route::post('/logout', 'logout')->name('logout');
    });
});

// Route::middleware(['auth', 'verified'])->group(function () {
// });
