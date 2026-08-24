<?php

namespace App\Http\Controllers;

use App\Enums\UserStatus;
use App\Http\Requests\EmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\AccountActivationNotification;
use App\Notifications\AccountPasswordResetNotification;
use App\Queries\EmployeeQuery;
use App\Services\EmployeeLifecycleService;
use App\Services\FileUploadService;
use App\Services\LocationService;
use App\Services\Shared\FormOptionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private const FILTER_KEYS = [
        'search',
        'status',
        'department_id',
        'position_id',
        'contract_type',
    ];

    public function __construct(
        protected FormOptionService $formOptionService,
        protected LocationService $locationService,
        private readonly FileUploadService $fileUploadService,
        private readonly EmployeeQuery $employeeQuery,
        private readonly EmployeeLifecycleService $employeeLifecycleService,
    ) {}

    public function index(Request $request): View
    {
        $employees = $this->employeeQuery->paginate(
            $request->only(self::FILTER_KEYS),
        );

        return view('employees.index', [
            'employees' => $employees,
            'departments' => $this->formOptionService->departmentOptions(),
            'positions' => $this->formOptionService->positionOptions(),
        ]);
    }

    public function create(): View
    {
        return view('employees.create', [
            'provinces' => $this->locationService->provinces(),
            'departments' => $this->formOptionService->departmentOptionsWithPositions(),
            'positions' => collect(),
            'employees' => $this->formOptionService->employeeOptions(),
        ]);
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                $employeeRequests = $request->all();
                if ($request->hasFile('avatar')) {
                    $employeeRequests['avatar'] = $this->fileUploadService->upload(
                        $request->file('avatar'),
                        'employees/avatars'
                    );
                }
                //  make user
                $user = User::create([
                    'name' => $employeeRequests['first_name'] . ' ' . $employeeRequests['last_name'],
                    'email' => $employeeRequests['email'],
                ]);
                // make employee
                $user->employee()->create($employeeRequests);
            });

            return redirect()
                ->route('employees.index')
                ->with('success', __('common.messages.created'));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.create_failed'));
        }
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'user',
            'department',
            'position',
            'reportingManager',
        ]);

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $employee->load([
            'user',
            'department',
            'position',
        ]);

        return view('employees.edit', [
            'employee' => $employee,
            'departments' => $this->formOptionService->departmentOptionsWithPositions(),
            'positions' => collect(),
            'employees' => $this->formOptionService->employeeOptions($employee),
            'provinces' => $this->locationService->provinces(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employeeRequests = $request->all();
        $oldAvatar = $employee->avatar;
        $newAvatar = null;
        try {
            DB::transaction(function () use (
                $employee,
                $employeeRequests,
                &$newAvatar
            ) {
                $employee->user->update([
                    'name' => trim( "{$employeeRequests['first_name']} {$employeeRequests['last_name']}"),
                    'email' => $employeeRequests['email'],
                ]);

                unset(
                    $employeeRequests['remove_avatar'],
                    $employeeRequests['avatar'],
                );

                if ($request->hasFile('avatar')) {
                    $newAvatar = $this->fileUploadService->upload(
                        $request->file('avatar'),
                        'employees/avatars'
                    );

                    $employeeRequests['avatar'] = $newAvatar;
                } elseif ($request->boolean('remove_avatar')) {
                    $employeeRequests['avatar'] = null;
                }

                $employee->update($employeeRequests);
            });

            if ( $oldAvatar && ($newAvatar || $request->boolean('remove_avatar'))) {
                $this->fileUploadService->delete($oldAvatar);
            }

            return back()->with('success', __('common.messages.updated'));
        } catch (\Throwable $e) {
            if ($newAvatar) {
                $this->fileUploadService->delete($newAvatar);
            }
            report($e);

            return back()
                ->withInput()
                ->with('error', __('common.messages.update_failed'));
        }
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            $this->employeeLifecycleService->softDeleteEmployee(
                $employee,
                auth()->user(),
            );

            return redirect()
                ->route('employees.index')
                ->with('success', __('common.messages.deleted'));
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', __('common.messages.delete_failed'));
        }
    }

    public function trash(Request $request): View
    {
        return view('employees.trash', [
            'employees' => $this->employeeQuery->paginateTrashed(
                $request->only(self::FILTER_KEYS),
            ),
            'departments' => $this->formOptionService->departmentOptions(),
            'positions' => $this->formOptionService->positionOptions(),
        ]);
    }

    public function restore(Employee $employee): RedirectResponse
    {
        try {
            DB::transaction(function () use ($employee) {
                $employee->restore();
            });

            return redirect()
                ->route('employees.trash')
                ->with('success', __('common.messages.restored'));

        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', __('common.messages.restore_failed'));
        }
    }

    public function resendActivation(Employee $employee, Request $request): RedirectResponse
    {
        $user = $employee->user;
        if ($user->activated_at) {
            return back()->with('error', __('common.messages.account_already_activated'));
        }
        // make new token
        $token = Str::random(64);
        $user->updateQuietly([
            'activation_token' => hash('sha256', $token),
            'activation_expires_at' => now()->addHours(24),
        ]);
        $user->notify(
            new AccountActivationNotification($token)
        );

        return back()->with('success', __('common.messages.activation_link_sent'));
    }

    public function resetAccountPassword(Employee $employee): RedirectResponse
    {
        $user = $employee->user;
        if (! $user) {
            return back()->with('error', __('common.messages.user_not_found'));
        }
        if ($user->status !== UserStatus::ACTIVE) {
            return back()->with('error', __('common.messages.account_not_active'));
        }
        $password = Str::password(12);
        $user->update(['password' => Hash::make($password)]);
        $user->notify(
            new AccountPasswordResetNotification($password)
        );

        return back()->with([
            'success' => __('common.messages.password_reset_sent'),
            'generated_password' => $password,
        ]);
    }
}
