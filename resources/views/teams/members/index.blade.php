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

    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                {{ __('site.teams.members') }}
                <span class="badge badge-soft-primary ml-1">{{ $team->current_members_count }}</span>
            </h4>
            <button
                type="button"
                class="btn btn-sm btn-outline-primary mt-2 mt-sm-0"
                data-toggle="modal"
                data-target="#add-member-modal"
            >
                <i class="fas fa-plus mr-1"></i>
                {{ __('site.teams.add_members') }}
            </button>
        </div>
        <div class="card-body">
            @include('teams._assignments-table', [
                'team' => $team,
                'assignments' => $memberships,
                'assignmentType' => 'member',
                'destroyRoute' => 'teams.members.destroy',
                'emptyMessage' => __('site.teams.no_current_members'),
            ])
        </div>
    </div>

    @include('teams._assignment-form', [
        'team' => $team,
        'employees' => $employees,
        'assignedEmployeeIds' => $assignedEmployeeIds,
        'assignmentType' => 'member',
        'storeAction' => route('teams.members.store', $team),
    ])
@endsection
