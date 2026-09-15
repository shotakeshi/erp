@push('title', __('site.teams.member_history'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.member_history') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.members.index', $team)],
            ['title' => __('site.teams.member_history')],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    @include('teams._member-history', ['historyRoute' => route('teams.members.history', $team)])
@endsection
