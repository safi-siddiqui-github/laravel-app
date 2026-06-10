<?php

use App\Enums\PersonalAccessToken\PersonalAccessTokenAbilityEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('client_security')->group(function () {

    Route::controller(AuthController::class)->group(function () {

        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('social-login', 'socialLogin');

        Route::prefix('forgot-password')->group(function () {
            Route::post('request', 'forgotPasswordRequest');
            Route::post('verify', 'forgotPasswordVerify');
            Route::post('resend', 'forgotPasswordResend');

            Route::middleware(
                [
                    'auth:sanctum',
                    'abilities:' . PersonalAccessTokenAbilityEnum::ALLOW_PASSWORD_RESET->value
                ]
            )
                ->group(function () {
                    Route::post('reset', 'forgotPasswordReset');
                });
        });

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

    Route::controller(ProfileController::class)
        ->prefix('profile')
        ->middleware('auth:sanctum')
        ->group(function () {

            Route::get('/current', 'currentUser');
        });


    Route::controller(BlogController::class)->prefix('blog')->group(function () {
        Route::prefix('posts')->group(function () {
            Route::get('/', 'getPosts');

            Route::middleware('auth:sanctum')->group(function () {
                Route::get('/get-current-user-posts', 'getCurrentUserPosts');
                Route::post('/store', 'storePost');
                Route::post('/delete-current-user-post', 'deleteCurrentUserPost');
            });
        });
    });
});
