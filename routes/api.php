<?php

use App\Enums\PersonalAccessToken\PersonalAccessTokenAbilityEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('client_security')->group(function () {

    Route::controller(AuthController::class)->group(function () {

        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('social-login', 'socialLogin');

        Route::middleware('auth:sanctum')->group(function () {

            Route::post('logout', 'logout');

            Route::prefix('email-verification')
                ->middleware(
                    'abilities:' . PersonalAccessTokenAbilityEnum::ALLOW_EMAIL_VERIFICATION->value
                )
                ->group(function () {
                    Route::post('verify', 'verifyEmail');
                    Route::post('resend', 'resendVerificationEmail');
                });
        });
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::controller(UserController::class)
            ->prefix('users')
            ->group(function () {

                Route::get('/current', 'currentUser');
            });
    });
});
