<?php

use App\Enums\TeamAssignmentType;
use App\Http\Controllers\Auth\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeTeamController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamAssignmentController;
use App\Http\Controllers\TeamController;
use App\Services\LocationService;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])
        ->name('login');
    Route::post('/sign-in', [LoginController::class, 'store'])
        ->name('login.store');
    Route::get('/account/activate/{token}', [AccountController::class, 'activate'])->name('account.activate');
    Route::post('/account/activate/{token}', [AccountController::class, 'completeActivation'])->name('account.activate.complete');
    Route::get('forgot', [AccountController::class, 'forgotPassword'])->name('account.forgot-password');
    Route::post('send-reset-link', [AccountController::class, 'sendResetLink'])->name('account.send-reset-link');
    Route::get('reset/{token}', [AccountController::class, 'resetPassword'])->name('password.reset');
    Route::post('reset', [AccountController::class, 'updatePassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');

    Route::prefix('employees')->name('employees.')->group(function () {
        Route::get('trash', [EmployeeController::class, 'trash'])->name('trash');
        Route::post('{employee}/restore', [EmployeeController::class, 'restore'])->withTrashed()->name('restore');
        Route::post('{employee}/resend-activation', [EmployeeController::class, 'resendActivation'])->withTrashed()->name('resend-activation');
        Route::post('{employee}/reset-password', [EmployeeController::class, 'resetAccountPassword'])->name('reset-password');
        Route::get('{employee}/teams', [EmployeeTeamController::class, 'index'])->name('teams.index');
        Route::get('{employee}/teams/history', [EmployeeTeamController::class, 'history'])->name('teams.history');
    });

    Route::prefix('teams')->name('teams.')->group(function () {
        Route::get('trash', [TeamController::class, 'trash'])->name('trash');
        Route::post('{team}/restore', [TeamController::class, 'restore'])->withTrashed()->name('restore');
    });

    Route::resource('teams', TeamController::class);

    Route::prefix('teams/{team}')->name('teams.')->group(function () {
        Route::get('members/history', [TeamAssignmentController::class, 'memberHistory'])->name('members.history');
        Route::get('managers/history', [TeamAssignmentController::class, 'managerHistory'])->name('managers.history');
        Route::get('members', [TeamAssignmentController::class, 'index'])
            ->defaults('type', TeamAssignmentType::MEMBER->value)
            ->name('members.index');
        Route::post('members', [TeamAssignmentController::class, 'memberStore'])->name('members.store');
        Route::delete('members/{employee}', [TeamAssignmentController::class, 'memberDestroy'])->name('members.destroy');
        Route::get('managers', [TeamAssignmentController::class, 'index'])
            ->defaults('type', TeamAssignmentType::MANAGER->value)
            ->name('managers.index');
        Route::post('managers', [TeamAssignmentController::class, 'managerStore'])->name('managers.store');
        Route::delete('managers/{employee}', [TeamAssignmentController::class, 'managerDestroy'])->name('managers.destroy');
    });

    Route::resources(
        [
            'employees' => EmployeeController::class,
            'departments' => DepartmentController::class,
            'positions' => PositionController::class,
        ]
    );

    Route::prefix('profiles')->name('profiles.')->group(function () {
        Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
        Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('update-password');
    });

    Route::get('/locations/wards/{province}', function (
        string $province,
        LocationService $locationService
    ) {
        return response()->json(
            $locationService->wardsByProvince($province)
        );
    });

    Route::post('/logout', [LoginController::class, 'destroy'])
        ->name('logout');
});
