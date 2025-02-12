<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepositAccountController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\WithdrawAccountController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('onboarding')
        ->group(function () {
            Route::controller(PinController::class)->group(function () {
                Route::post('setup/pin', 'setupPin');
                Route::middleware('has.set.pin')
                    ->post('validate/pin', 'validatePin');
            });
            Route::middleware('has.set.pin')
                ->post('generate/account_number', [
                    AccountController::class,
                    'createAccountNumber'
                ]);
        });

    Route::middleware('has.set.pin')
        ->prefix('account')
        ->group(function () {
            Route::post('deposit', [DepositAccountController::class, 'store']);
            Route::post('withdraw', [WithdrawAccountController::class, 'store']);
            Route::post('transfer', [TransferController::class, 'store']);
        });
});
