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
                <th style="width: 200px" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $employee)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="media">
                            <a class="" href="{{ route('employees.show', $employee) }}">
                                <img src="{{ image_url($employee->avatar) }}" alt="{{ $employee->fullname }}" class="rounded-circle thumb-md">
                            </a>
                            <div class="media-body align-self-center ml-3">
                                <p class="font-14 font-weight-bold mb-0">{{ $employee->fullname }}</p>
                                <p class="mb-0 font-12 text-muted">{{ $employee->email }}</p>
                            </div>
                        </div><!--end media-->
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
                    <td class="text-center">
                        <div class="dropdown d-inline-block float-right">
                            <a style="width: 34px" href="{{ route('employees.show', $employee)  }}" class="btn btn-sm btn-outline-gray">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if(request()->routeIs('employees.trash'))
                                <form action="{{ route('employees.restore', $employee) }}" method="POST" class="d-inline" >
                                    @csrf
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-success"
                                        title="{{ __('common.button.restore') }}"
                                        onclick="return confirm('{{ __('common.messages.confirm_restore') }}')" >
                                        <i class="fas fa-undo"></i> {{ __('common.button.restore') }}
                                    </button>
                                </form>
                            @else
                                <a style="width: 34px" href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <x-form.delete-button
                                    :action="route('employees.destroy',$employee)"
                                />
                                <div class="btn-group mt-2" style="width: 112px">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Account <i class="mdi mdi-chevron-down"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class="dropdown-item" href="#">Something else here</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="#">Separated link</a>
                                    </div>
                                </div><!-- /btn-group -->
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table><!--end /table-->
</div><!--end /tableresponsive-->