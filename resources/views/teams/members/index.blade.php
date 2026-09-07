@push('title', __('site.teams.current_members'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.current_members') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.show', $team)],
            ['title' => __('site.teams.members')],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    @include('teams._members-toolbar', ['team' => $team])

    <div class="card">
        <div class="card-body">
            <div class="table-responsive mt-3">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                        <tr>
                            <th>{{ __('site.teams.employee') }}</th>
                            <th>{{ __('site.teams.employee_code') }}</th>
                            <th>{{ __('site.teams.department') }}</th>
                            <th>{{ __('site.teams.position') }}</th>
                            <th>{{ __('site.teams.start_date') }}</th>
                            <th>{{ __('site.teams.role') }}</th>
                            <th class="text-center">{{ __('site.teams.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($memberships as $assignment)
                            @php
                                $employee = $assignment->employee;
                            @endphp
                            <tr data-member-search="{{ $employee->full_name }} {{ $employee->position?->name }}">
                                <td>
                                    <div class="media align-items-center">
                                        <span class="avatar-box thumb-sm align-self-center mr-2">
                                            <span class="avatar-title bg-soft-info rounded-circle">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </span>
                                        <div class="media-body">
                                            @if ($employee->trashed())
                                                <span class="font-weight-bold">{{ $employee->full_name }}</span>
                                            @else
                                                <a href="{{ route('employees.show', $employee) }}" class="font-weight-bold text-primary">
                                                    {{ $employee->full_name }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $employee->employee_id }}</td>
                                <td>{{ $employee->department?->name ?? '-' }}</td>
                                <td>{{ $employee->position?->name ?? '-' }}</td>
                                <td class="text-nowrap">{{ $assignment->start_date->format('d/m/Y') }}</td>
                                <td>{{ $assignment->role ?: '-' }}</td>
                                <td class="text-center">
                                    @if ($employee->trashed())
                                        <span class="text-muted font-12" title="{{ __('site.teams.deleted_employee_action_unavailable') }}">
                                            <i class="fas fa-lock mr-1"></i>
                                            {{ __('site.teams.action_unavailable') }}
                                        </span>
                                    @else
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-toggle="modal"
                                            data-target="#remove-assignment-modal"
                                            data-assignment-action="{{ route('teams.members.destroy', [$team, $employee]) }}"
                                            data-employee-id="{{ $employee->id }}"
                                            data-employee-name="{{ $employee->full_name }}"
                                            data-start-date="{{ $assignment->start_date->toDateString() }}"
                                            data-assignment-description="{{ __('site.teams.remove_member_confirmation', ['employee' => $employee->full_name]) }}"
                                            title="{{ __('site.teams.remove_member_title') }}"
                                        >
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-user-friends d-block font-20 mb-2"></i>
                                    {{ __('site.teams.no_current_members') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('teams._remove-assignment-modal', [
            'team' => $team,
            'assignments' => $memberships,
            'destroyRoute' => 'teams.members.destroy',
        ])
    </div>

    @include('teams._add_assignment-modal', [
        'team' => $team,
        'employees' => $employees,
        'storeAction' => route('teams.members.store', $team),
    ])
@endsection
