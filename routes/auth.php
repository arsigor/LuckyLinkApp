<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegisteredUserController::class, 'create'])
    ->name('homepage');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');
