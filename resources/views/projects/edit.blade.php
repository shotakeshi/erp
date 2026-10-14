@push('title', __('site.projects.edit'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.projects.edit') }}"
        :breadcrumbs="[
            ['title' => __('site.projects.title'), 'url' => route('projects.index')],
            ['title' => $project->name, 'url' => route('projects.show', $project)],
            ['title' => __('site.projects.edit')],
        ]"
    />

    <div class="row">
        <div class="col-lg-8 mx-auto">
            @include('projects.partials._form-upsert', [
                'project' => $project,
                'action' => route('projects.update', $project),
                'method' => 'PUT',
            ])
        </div>
    </div>
@endsection
