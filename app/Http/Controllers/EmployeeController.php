<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\LocationService;
use App\Http\Requests\EmployeeRequest;
use App\Services\Shared\FormOptionService;
use App\Services\FileUploadService;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct(
        protected FormOptionService $formOptionService,
        protected LocationService $locationService,
        private readonly FileUploadService $fileUploadService
    ){

    }
    public function index(Request $request, ): View
    {
        $employees = Employee::with('user','department','position')->paginate(20);
        return view('employees.index',[
            'employees' => $employees
        ]);
    }

    public function create(){
        return view('employees.create', [
            'provinces' => $this->locationService->provinces(),
            'departments' => $this->formOptionService->departmentOptionsWithPositions(),
            'positions' => collect(),
            'employees' => $this->formOptionService->employeeOptions()
        ]);
    }

    public function store(EmployeeRequest $request)
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
}
