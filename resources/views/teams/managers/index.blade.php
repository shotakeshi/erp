@push('title', __('site.teams.current_managers'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.current_managers') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.show', $team)],
            ['title' => __('site.teams.managers')],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                {{ __('site.teams.managers') }}
                <span class="badge badge-soft-primary ml-1">{{ $team->current_managers_count }}</span>
            </h4>
            <button
                type="button"
                class="btn btn-sm btn-outline-primary mt-2 mt-sm-0"
                data-toggle="modal"
                data-target="#add-manager-modal"
            >
                <i class="fas fa-plus mr-1"></i>
                {{ __('site.teams.add_managers') }}
            </button>
        </div>
        <div class="card-body">
            @include('teams._assignments-table', [
                'team' => $team,
                'assignments' => $managerAssignments,
                'assignmentType' => 'manager',
                'destroyRoute' => 'teams.managers.destroy',
                'emptyMessage' => __('site.teams.no_current_managers'),
            ])
        </div>
    </div>

    @include('teams._assignment-form', [
        'team' => $team,
        'employees' => $employees,
        'assignedEmployeeIds' => $assignedEmployeeIds,
        'assignmentType' => 'manager',
        'storeAction' => route('teams.managers.store', $team),
    ])
@endsection
