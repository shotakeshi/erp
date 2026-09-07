<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="media align-items-center">
                    <span class="avatar-box thumb-lg align-self-center mr-3">
                        <span class="avatar-title bg-soft-info rounded-circle font-20">
                            <i class="fas fa-user"></i>
                        </span>
                    </span>
                    <div class="media-body">
                        <h4 class="header-title mb-1">{{ $employee->full_name }}</h4>
                        <span class="badge badge-light">{{ $employee->employee_id }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 mt-3 mt-lg-0 text-lg-right">
                <a href="{{ route('employees.teams.index', $employee) }}" class="btn btn-sm btn-outline-primary mb-1">
                    <i class="fas fa-users mr-1"></i>
                    {{ __('site.teams.current_teams') }}
                </a>
                <a href="{{ route('employees.teams.history', $employee) }}" class="btn btn-sm btn-outline-info mb-1">
                    <i class="fas fa-history mr-1"></i>
                    {{ __('site.teams.team_history') }}
                </a>
            </div>
        </div>
    </div>
</div>
