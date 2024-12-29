<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/resend', [AuthController::class, 'resend']);
Route::get('email/verify/{id}/{hash}', [AuthController::class, '__invoke'])
->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
