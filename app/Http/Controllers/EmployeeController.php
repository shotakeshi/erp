<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Services\LocationService;
use App\Http\Requests\EmployeeRequest;
use App\Services\Shared\FormOptionService;
use App\Services\FileUploadService;
use App\Models\User;
use App\Filters\EmployeeFilter;
use App\Enums\UserStatus;
use App\Models\Employee;

class EmployeeController extends Controller
{
    public function __construct(
        protected FormOptionService $formOptionService,
        protected LocationService $locationService,
        private readonly FileUploadService $fileUploadService,
        private readonly EmployeeFilter $employeeFilter,
    ){

    }
    public function index(Request $request): View
    {
        $employees = $this->employeeFilter
            ->apply(
                Employee::query(),
                $request->only([
                    'search',
                    'status',
                    'department_id',
                    'position_id',
                    'contract_type',
                ])
            )
            ->with([
                'user',
                'department',
                'position',
            ])
            ->paginate(20)
            ->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'departments' => $this->formOptionService->departmentOptions(),
            'positions' => $this->formOptionService->positionOptions()
        ]);
    }

    public function create(): View
    {
        return view('employees.create', [
            'provinces' => $this->locationService->provinces(),
            'departments' => $this->formOptionService->departmentOptionsWithPositions(),
            'positions' => collect(),
            'employees' => $this->formOptionService->employeeOptions()
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
            'employees' => $this->formOptionService->employeeOptions(),
            'provinces' => $this->locationService->provinces(),
        ]);
    }

    public function update(EmployeeRequest $request, Employee $employee  ): RedirectResponse
    {
        $employeeRequests = $request->all();
        $oldAvatar = $employee->avatar;
        $newAvatar = null;
        try {
            DB::transaction(function () use (
                $request,
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
            return back()->with( 'success',  __('common.messages.updated'));
        } catch (\Throwable $e) {
            if ($newAvatar) {
                $this->fileUploadService->delete($newAvatar);
            }
            report($e);
            return back()
                ->withInput()
                ->with( 'error', __('common.messages.update_failed'));
        }
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            $employee->user->update([
                'status' => UserStatus::BLOCKED->value,
            ]);
            $employee->delete();
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
        $employees = $this->employeeFilter
            ->apply(
                Employee::query(),
                $request->only([
                    'search',
                    'status',
                    'department_id',
                    'position_id',
                    'contract_type',
                ])
            )
            ->onlyTrashed()
            ->with([
                'user',
                'department',
                'position',
            ])
            ->paginate(20)
            ->withQueryString();

        return view('employees.trash', [
            'employees' => $employees,
            'departments' => $this->formOptionService->departmentOptions(),
            'positions' => $this->formOptionService->positionOptions()
        ]);
    }

    public function restore(Employee $employee): RedirectResponse
    {
        try {
            DB::transaction(function () use ($employee) {
                $employee->restore();
                $employee->user->update([
                    'status' => UserStatus::INACTIVE->value,
                ]);
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

}
