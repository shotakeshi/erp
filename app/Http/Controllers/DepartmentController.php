<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Shared\FormOptionService;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Support\Facades\Log;
use Throwable;

class DepartmentController extends Controller
{
    public function __construct(
        protected FormOptionService $formOptionService
    ){

    }
    public function index(Request $request): View
    {
        $departments = Department::with('parent','head')
            ->withCount('employees')->get();
        return view('departments.index', compact('departments'));
    }

    public function create(){
        return view('departments.create', [
            'employees' => $this->formOptionService->employeeOptions(),
            'departments' => $this->formOptionService->departmentOptions()
        ]);
    }

    public function store(DepartmentRequest $request)
    {
        try {
            Department::create($request->all());
            return redirect()
                ->route('departments.index')
                ->with('success', __('common.messages.created'));
        } catch (Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', __('common.messages.create_failed'));
        }
    }

    public function show(Department $department){
        return view('departments.show', [
            'department' => $department
        ]);
    }

    public function edit(Department $department){
        return view('departments.edit', [
            'department' => $department,
            'employees' => $this->formOptionService->employeeOptions(),
            'departments' => $this->formOptionService->departmentForParentSelect($department)
        ]);
    }

    public function update(DepartmentRequest $request, Department $department){
        try {
            $department->fill($request->all());
            if (! $department->isDirty()) {
                return back()->with('warning', __('common.messages.not_changed'));
            }
            $department->update($request->all());
            return redirect()
                ->route('departments.index')
                ->with('success', __('common.messages.updated'));
        } catch (Throwable $e) {
            report($e);
            return back()
                ->withInput()
                ->with('error', __('common.messages.update_failed'));
        }
    }
}
