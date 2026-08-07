<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\LocationService;
use App\Http\Requests\EmployeeRequest;
use App\Services\Shared\FormOptionService;

class EmployeeController extends Controller
{
    public function __construct(
        protected FormOptionService $formOptionService,
        protected LocationService $locationService
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

    public function store(EmployeeRequest $request){
        dd($request->all());
    }
}
