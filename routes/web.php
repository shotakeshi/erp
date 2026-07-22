<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'create'])
        ->name('login');
    Route::post('/', [LoginController::class, 'store'])
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (){
        return view('welcome');
    })->name('dashboard');

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');
});