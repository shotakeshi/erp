@push('title', __('site.teams.add'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.add') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => __('site.teams.add')],
        ]"
    />

    <div class="row">
        <div class="col-lg-12">
            @include('teams._form', [
                'action' => route('teams.store'),
                'method' => 'POST',
            ])
        </div>
    </div>
@endsection
