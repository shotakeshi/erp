@push('title', __('site.teams.team_history'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.team_history') }}"
        :breadcrumbs="[
            ['title' => __('site.employees.title'), 'url' => route('employees.index')],
            ['title' => $employee->full_name],
            ['title' => __('site.teams.team_history')],
        ]"
    />

    @include('employees.teams._employee-summary', ['employee' => $employee])

    <div class="card">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('site.teams.team_history') }}</h4>
        </div>
        <div class="card-body">
            @include('employees.teams._memberships-table', [
                'memberships' => $memberships,
                'emptyMessage' => __('site.teams.no_team_history'),
                'showPagination' => true,
            ])
        </div>
    </div>
@endsection
