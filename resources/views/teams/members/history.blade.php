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

    <div class="row mb-3">
        <div class="col-lg-6">
            <a href="{{ route('teams.members.index', $team) }}" class="btn btn-sm btn-outline-gray">
                <i class="fas fa-arrow-left mr-1"></i>
                {{ __('site.teams.back_to_member_list') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">{{ __('site.teams.member_history') }}</div>
        <div class="card-body">
            @include('teams._member-history-table', [
                'memberships' => $memberships,
                'filterAction' => route('teams.members.history', $team),
            ])
        </div>
    </div>
@endsection
