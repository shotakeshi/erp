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

    <div class="row mb-3">
        <div class="col-lg-6">
            <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-gray">
                <i class="fas fa-arrow-left mr-1"></i>
                {{ __('site.teams.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="card">
        @include('teams._assignments-table', [
            'team' => $team,
            'assignments' => $managerAssignments,
            'assignmentType' => 'manager',
            'destroyRoute' => 'teams.managers.destroy',
            'emptyMessage' => __('site.teams.no_current_managers'),
        ])
    </div>

    @include('teams._add_assignment-modal', [
        'team' => $team,
        'employees' => $employees,
        'assignedEmployeeIds' => $assignedEmployeeIds,
        'assignmentType' => 'manager',
        'storeAction' => route('teams.managers.store', $team),
    ])
@endsection
