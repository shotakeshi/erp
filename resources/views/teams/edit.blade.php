@push('title', __('site.teams.edit'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.edit') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.show', $team)],
            ['title' => __('site.teams.edit')],
        ]"
    />

    <div class="row">
        <div class="col-lg-8 mx-auto">
            @include('teams._form-upsert', [
                'team' => $team,
                'action' => route('teams.update', $team),
                'method' => 'PUT',
            ])
        </div>
    </div>
@endsection
