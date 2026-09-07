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
            'assignments' => $memberships,
            'assignmentType' => 'member',
            'destroyRoute' => 'teams.members.destroy',
            'emptyMessage' => __('site.teams.no_current_members'),
        ])
    </div>

    @include('teams._add_assignment-modal', [
        'team' => $team,
        'employees' => $employees,
        'assignedEmployeeIds' => $assignedEmployeeIds,
        'assignmentType' => 'member',
        'storeAction' => route('teams.members.store', $team),
    ])
@endsection
