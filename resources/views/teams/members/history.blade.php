@push('title', __('site.teams.member_history'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.member_history') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.show', $team)],
            ['title' => __('site.teams.member_history')],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('site.teams.member_history') }}</h4>
        </div>
        <div class="card-body">
            @include('teams._member-history-table', [
                'memberships' => $memberships,
                'filterAction' => route('teams.members.history', $team),
            ])
        </div>
    </div>
@endsection
