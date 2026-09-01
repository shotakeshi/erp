@push('title', __('site.teams.manager_history'))
@extends('layouts.master')

@section('content')
    <x-page-title
            title="{{ __('site.teams.manager_history') }}"
            :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.show', $team)],
            ['title' => __('site.teams.manager_history')],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    <div class="row mb-3">
        <div class="col-lg-6">
            <a href="{{ route('teams.managers.index', $team) }}" class="btn btn-sm btn-outline-gray">
                <i class="fas fa-arrow-left mr-1"></i>
                {{ __('site.teams.back_to_manager_list') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">{{ __('site.teams.manager_history') }}</div>
        <div class="card-body">
            @include('teams._history-table', [
                'memberships' => $memberships,
                'filterAction' => route('teams.managers.history', $team),
                'emptyMessage' => __('site.teams.no_manager_history'),
            ])
        </div>
    </div>
@endsection
