<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Queries\TeamQuery;
use Illuminate\View\View;

class EmployeeTeamController extends Controller
{
    public function __construct(private readonly TeamQuery $teamQuery) {}

    public function index(Employee $employee): View
    {
        return view('employees.teams.index', [
            'employee' => $employee,
            'memberships' => $this->teamQuery->employeeCurrentTeams($employee),
        ]);
    }

    public function history(Employee $employee): View
    {
        return view('employees.teams.history', [
            'employee' => $employee,
            'memberships' => $this->teamQuery->employeeTeamHistory($employee),
        ]);
    }
}
