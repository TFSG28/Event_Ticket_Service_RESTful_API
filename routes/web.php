<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReservationController;

// Events routes
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{event}', [EventController::class, 'show']);
Route::post('/events', [EventController::class, 'store']);
Route::put('/events/{event}', [EventController::class, 'update']);
Route::delete('/events/{event}', [EventController::class, 'destroy']);

// Reservations routes
Route::get('/reservations', [ReservationController::class, 'index']);
Route::get('/reservations/{reservation}', [ReservationController::class, 'show']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::put('/reservations/{reservation}', [ReservationController::class, 'update']);
Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy']);
