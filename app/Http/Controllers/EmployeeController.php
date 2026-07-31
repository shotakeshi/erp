<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\LocationService;

class EmployeeController extends Controller
{
    public function index(Request $request, ): View
    {
        $employees = Employee::with('user','department','position')->paginate(20);
        return view('employees.index', compact('employees'));
    }

    public function create(LocationService $locationService){
        return view('employees.create', [
            'provinces' => $locationService->provinces(),
        ]);
    }
}
