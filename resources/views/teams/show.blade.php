@push('title', $team->name)
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.detail') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    @php
        $assignments = $team->assignments;
    @endphp

    <div class="row mt-3">
        <div class="col-lg-12">
            @include('teams._members-toolbar', ['team' => $team])
        </div>
    </div>
    <div class="row">
            @forelse ($assignments as $assignment)
                @php
                    $employee = $assignment->employee;
                @endphp

                <div class="col-sm-6 col-lg-3 mb-3" data-member-search="{{ $employee->full_name }} {{ $employee->position?->name }}">
                    <div class="card team-card">
                        <div class="card-body text-center">
                            <div class="d-flex justify-content-end">
                                <div class="dropdown d-inline-block">
                                    <a
                                            type="button"
                                            class="nav-link dropdown-toggle arrow-none"
                                            id="team-actions-{{ $assignment->id }}"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                    >
                                        <i class="fas fa-ellipsis-v font-20 text-muted"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="team-actions-{{ $assignment->id }}">
                                        <a class="dropdown-item text-gray" href="{{ route('employees.show', $employee) }}">
                                            {{ __('common.button.view') }}
                                        </a>
                                        <a
                                            type="button"
                                            class="dropdown-item text-danger"
                                            data-toggle="modal"
                                            data-target="#remove-assignment-modal"
                                            data-assignment-action="{{ route('teams.members.destroy', [$team, $employee]) }}"
                                            data-employee-id="{{ $employee->id }}"
                                            data-employee-name="{{ $employee->full_name }}"
                                            data-start-date="{{ $assignment->start_date->toDateString() }}"
                                            data-assignment-description="{{ __('site.teams.remove_member_confirmation', ['employee' => $employee->full_name]) }}"
                                            title="{{ __('site.teams.remove_member_title') }}"
                                            aria-label="{{ __('site.teams.remove') }}"
                                        >
                                            {{ __('site.teams.remove') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <img
                                    src="{{ image_url($employee->avatar) }}"
                                    alt="{{ $employee->full_name }}"
                                    class="rounded-circle thumb-xl d-block mx-auto mb-2"
                            >

                            <h5 class="client-name mb-1">
                                @if ($employee->trashed())
                                    <span>{{ $employee->full_name }}</span>
                                @else
                                    <a href="{{ route('employees.edit', $employee) }}" class="text-primary">
                                        {{ $employee->full_name }}
                                    </a>
                                @endif
                            </h5>

                            <div class="mb-2">
                                    <span class="badge badge-light">
                                        {{ __('site.teams.employee_code') }}: {{ $employee->employee_id }}
                                    </span>
                            </div>

                            <div class="font-12 text-muted text-nowrap mb-2">
                                    <span class="mr-2">
                                        <i class="fas fa-envelope text-info mr-1" aria-hidden="true"></i>
                                        {{ $employee->email ?: '-' }}
                                    </span>
                                <span>
                                        <i class="fas fa-phone text-info mr-1" aria-hidden="true"></i>
                                        {{ $employee->phone ?: '-' }}
                                    </span>
                            </div>

                            <p class="font-13 mb-3">
                                <span class="text-muted">{{ __('site.teams.role') }}:</span>
                                {{ $assignment->role ?: '-' }}
                            </p>

                            <div>
                                @if ($employee->trashed())
                                    <span class="text-muted font-12" title="{{ __('site.teams.deleted_employee_action_unavailable') }}">
                                            <i class="fas fa-lock mr-1" aria-hidden="true"></i>
                                            {{ __('site.teams.action_unavailable') }}
                                        </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-user-friends d-block font-20 mb-2" aria-hidden="true"></i>
                        {{ __('site.teams.no_current_members') }}
                    </div>
                </div>
            @endforelse
        </div>

    @include('teams._remove-assignment-modal', [
        'team' => $team,
        'assignments' => $assignments,
        'destroyRoute' => 'teams.members.destroy',
    ])

    @include('teams._add_assignment-modal', [
        'team' => $team,
        'employees' => $employees,
        'storeAction' => route('teams.members.store', $team),
    ])

@endsection

@include('teams._confirmation-assets')
