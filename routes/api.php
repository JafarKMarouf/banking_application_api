<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PinController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['prefix' => 'auth'], function () {
    Route::controller(AuthController::class)
        ->group(function () {
            Route::post('register', 'register');
            Route::post('login', 'login');

            Route::middleware('auth:sanctum')
                ->group(function () {
                    Route::get('logout', 'logout');
                    Route::get('user', 'user');
                });
        });
});

Route::middleware('auth:sanctum')
    ->prefix('onboarding/')
    ->group(function () {
        Route::controller(PinController::class)
            ->group(function () {
                Route::post('setup/pin', 'setupPin');
                Route::post('validate/pin', 'validatePin');
                Route::post('has_pin/pin', 'hasSetPIN');
            });
    });
