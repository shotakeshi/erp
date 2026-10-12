@push('title', __('site.projects.add'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.projects.add') }}"
        :breadcrumbs="[
            ['title' => __('site.projects.title'), 'url' => route('projects.index')],
            ['title' => __('site.projects.add')],
        ]"
    />

    <div class="row">
        <div class="col-lg-8 mx-auto">
            @include('projects._form-upsert', [
                'action' => route('projects.store'),
                'method' => 'POST',
            ])
        </div>
    </div>
@endsection
