@props([
    'employee',
])

@if ($employee->trashed())
    <span class="badge badge-pill badge-danger pl-2 pr-2">
        <i class="fas fa-user-slash mr-1"></i>
        {{ __('site.teams.employee_deleted') }}
    </span>
@elseif ($employee->user === null)
    <span class="badge badge-pill badge-dark pl-2 pr-2">
        <i class="fas fa-exclamation-triangle mr-1"></i>
        {{ __('site.teams.missing_user') }}
    </span>
@else
    <span class="{{ $employee->user->status->badgeClass() }}">
        <i class="{{ $employee->user->status->icon() }}"></i>
        {{ $employee->user->status->label() }}
    </span>
@endif
