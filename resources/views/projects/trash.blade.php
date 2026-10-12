@push('title', __('site.projects.title'))
@extends('layouts.master')

@section('content')
    <x-page-title
        :title="__('site.projects.title')"
        :breadcrumbs="[
            ['title' => __('site.projects.title'), 'url' => route('projects.index')],
            ['title' => __('common.button.list_of_trash')],
        ]"
    />

    <div class="card">
        <div class="card-body">
            <a href="{{ route('projects.index') }}" class="btn btn-sm btn-outline-gray">
                <i class="fas fa-arrow-left mr-1" aria-hidden="true"></i>
                {{ __('site.projects.back_to_list') }}
            </a>
            <form action="{{ route('projects.trash') }}" method="GET" class="mt-3">
                <div class="row">
                    <div class="col-lg-4">
                        <x-form.input name="search" :value="request('search')" placeholder="{{ __('site.projects.search_placeholder') }}" />
                    </div>
                    <div class="col-lg-8">
                        <button type="submit" class="btn btn-outline-gray mr-3">{{ __('common.button.search') }}</button>
                        <a href="{{ route('projects.trash') }}" class="btn btn-outline-danger">{{ __('common.button.reset') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                        <tr>
                            <th>{{ __('site.projects.code') }}</th>
                            <th>{{ __('site.projects.name') }}</th>
                            <th>{{ __('site.projects.description') }}</th>
                            <th class="text-center">{{ __('site.projects.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td><span class="badge badge-classic">{{ $project->code }}</span></td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->description ?: '-' }}</td>
                                <td class="text-center">
                                    <x-form.confirm-button
                                        :action="route('projects.restore', $project)"
                                        title="{{ __('common.messages.restore_confirm') }}"
                                        text="{{ __('common.messages.restore_sure') }}"
                                        confirm-text="{{ __('common.button.restore') }}"
                                        cancel-text="{{ __('common.button.cancel') }}"
                                        icon="fas fa-undo"
                                        class="btn btn-sm btn-outline-success"
                                        label="{{ __('common.button.restore') }}"
                                    />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">{{ __('site.projects.no_projects') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <x-pagination :paginator="$projects" />
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
@endpush
