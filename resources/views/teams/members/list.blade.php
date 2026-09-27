@push('title', __('site.teams.current_members'))
@extends('layouts.master')

@section('content')
    <x-page-title
            title="{{ __('site.teams.current_members') }}"
            :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.members.index', $team)],
            ['title' => __('site.teams.members')],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    @include('teams.members._toolbar', ['team' => $team, 'mode' => $mode])

    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                    <tr>
                        <th>{{ __('site.teams.employee_code') }}</th>
                        <th>{{ __('site.teams.employee') }}</th>
                        <th>{{ __('site.teams.department') }}</th>
                        <th>{{ __('site.teams.role') }}</th>
                        <th>{{ __('site.teams.start_date') }}</th>
                        <th class="text-center">{{ __('site.teams.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($assignments as $assignment)
                        @php
                            $employee = $assignment->employee;
                            $employeeIsDeleted = $employee->trashed();
                        @endphp
                        <tr>
                            <td>#{{ $employee->employee_id }}</td>
                            <td>
                                <div class="media align-items-center">
                                    <div class="media">
                                        @if ($employeeIsDeleted)
                                            <img src="{{ image_url($employee->avatar) }}"
                                                 alt="{{ $employee->full_name }}" class="rounded-circle thumb-md">
                                        @else
                                            <a class="" href="{{ route('employees.show', $employee) }}">
                                                <img src="{{ image_url($employee->avatar) }}"
                                                     alt="{{ $employee->full_name }}" class="rounded-circle thumb-md">
                                            </a>
                                        @endif
                                        <div class="media-body align-self-center ml-3">
                                            @if ($employeeIsDeleted)
                                                <span class="font-weight-bold">{{ $employee->full_name }}</span>
                                                <span class="badge badge-soft-danger ml-1">
                                                         <i class="ti ti-close mr-1" aria-hidden="true"></i>{{ __('site.teams.employee_deleted') }}
                                                    </span>
                                            @else
                                                <a href="{{ route('employees.show', $employee) }}"
                                                   class="font-weight-bold text-primary">
                                                    {{ $employee->full_name }}
                                                </a>
                                            @endif
                                            <p class="mb-0 font-12 text-muted">{{ $employee->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->department?->name ?? '--' }}
                                - {{ $employee->position?->name ?? '--' }}</td>
                            <td>{{ $assignment->role ?: '-' }}</td>
                            <td class="text-nowrap">{{ $assignment->start_date->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-toggle="modal"
                                        data-target="#remove-assignment-modal"
                                        data-assignment-action="{{ route('teams.members.destroy', [$team, $employee]) }}"
                                        data-employee-id="{{ $employee->id }}"
                                        data-start-date="{{ $assignment->start_date->toDateString() }}"
                                        data-assignment-description="{{ __('site.teams.remove_member_confirmation', ['employee' => $employee->full_name]) }}"
                                        title="{{ __('site.teams.remove_member_title') }}"
                                >
                                    <i class="fas fa-user-minus"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-user-friends d-block font-20 mb-2"></i>
                                {{ __('site.teams.no_current_members') }}
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$assignments"/>
        </div>

        @include('teams._remove-assignment-modal', [
            'team' => $team,
            'assignments' => $assignments->getCollection(),
            'destroyRoute' => 'teams.members.destroy',
        ])
    </div>

    @include('teams._add_assignment-modal', [
        'team' => $team,
        'employees' => $employees,
        'storeAction' => route('teams.members.store', $team),
    ])
@endsection
