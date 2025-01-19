<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ReservationController;
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

Route::controller(AuthController::class)->prefix('v1')->group(function(){
    Route::post('login', 'login')->name('login');
    Route::post('logout', [AuthController::class, "logout"])->name('logout');
});

Route::middleware('auth:sanctum')->prefix('v1')->group( function () {
    Route::post('register', [AuthController::class, "register"])->name('register');
    Route::resource('events', EventController::class);
    Route::resource('reservations', ReservationController::class);
});
