<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AccountController;

Route::get('/', function () {
    return redirect('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');
    Route::post('/sign-in', [LoginController::class, 'store'])
        ->name('login.store');
    Route::get('/account/activate/{token}', [AccountController::class,'activate'])->name('account.activate');
    Route::post('/account/activate/{token}', [AccountController::class,'completeActivation'])->name('account.activate.complete');
    Route::get('forgot', [AccountController::class,'forgotPassword',])->name('account.forgot-password');
    Route::post('send-reset-link', [AccountController::class, 'sendResetLink'])->name('account.send-reset-link');
    Route::get('reset/{token}', [AccountController::class, 'resetPassword'])->name('password.reset');
    Route::post('reset', [AccountController::class, 'updatePassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function (){
        return view('welcome');
    })->name('dashboard');

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('trash', [EmployeeController::class, 'trash'])->name('trash');
        Route::post('{employee}/restore', [EmployeeController::class, 'restore'])->withTrashed()->name('restore');
    });

    Route::resources(
        [
            'employees' => EmployeeController::class,
            'departments' => DepartmentController::class,
            'positions' => PositionController::class,
        ]
    );

    Route::prefix('profiles')->name('profiles.')->group(function () {
        Route::get('/change-password', [ProfileController::class,'changePassword',])->name('change-password');
        Route::put('/profile/change-password', [ ProfileController::class,'updatePassword',])->name('update-password');
    });

    Route::get('/locations/wards/{province}', function (
        string $province,
        App\Services\LocationService $locationService
    ) {
        return response()->json(
            $locationService->wardsByProvince($province)
        );
    });

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');
});