<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(Request $request): View
    {
        $departments = Department::all();
        return view('departments.index', compact('departments'));
    }

    public function create(){
        $employees = Employee::query()
            ->select([
                'id',
                'employee_id',
                'first_name',
                'last_name',
            ])
            ->get();
        dd($employees);
        return view('departments.create');
    }
}
