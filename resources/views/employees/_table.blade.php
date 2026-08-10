<div class="table-responsive mt-3">
    <table class="table table-bordered mb-0 table-centered">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('site.employees.member_name') }}</th>
                <th>{{ __('site.employees.sku') }}</th>
                <th>{{ __('site.employees.department') }}</th>
                <th>{{ __('site.employees.position') }}</th>
                <th>{{ __('site.employees.joined') }}</th>
                <th>{{ __('site.employees.employee_status') }}</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $employee->fullname }}<br/>
                        <span class="text-muted">{{ $employee->email }}</span>
                    </td>
                    <td>{{ $employee->employee_id }}</td>
                    <td>{{ $employee->department?->name ?? '-' }}</td>
                    <td>{{ $employee->position?->name ?? '-' }}</td>
                    <td>{{ $employee->date_of_joining?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        <span class="{{ $employee->user->status?->badgeClass() }}">
                            <i class="{{ $employee->user->status?->icon() }}"></i>
                            {{ $employee->user->status?->label() }}
                        </span>
                    </td>
                    <td>
                        <div class="dropdown d-inline-block float-right">
                            <a class="nav-link dropdown-toggle arrow-none" id="dLabel8" data-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                <i class="fas fa-ellipsis-v font-20 text-muted"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel8">
                                <a class="dropdown-item" href="#">Creat Project</a>
                                <a class="dropdown-item" href="#">Open Project</a>
                                <a class="dropdown-item" href="#">Tasks Details</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table><!--end /table-->
</div><!--end /tableresponsive-->