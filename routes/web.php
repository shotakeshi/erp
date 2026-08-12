<?php

use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PositionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return redirect('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');
    Route::post('/sign-in', [LoginController::class, 'store'])
        ->name('login.store');
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