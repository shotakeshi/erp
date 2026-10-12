@push('title', __('site.projects.title'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.projects.title') }}"
        :breadcrumbs="[
            ['title' => __('site.projects.title'), 'url' => route('projects.index')],
            ['title' => __('site.projects.list')],
        ]"
    />

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <x-action-button icon="fas fa-plus" type="primary" :href="route('projects.create')">
                                {{ __('site.projects.add') }}
                            </x-action-button>
                        </div>
                        <div class="col-lg-6 text-lg-right mt-2 mt-lg-0">
                            <a href="{{ route('projects.trash') }}" class="btn btn-sm btn-outline-gray">
                                <i class="fas fa-trash mr-1" aria-hidden="true"></i>
                                {{ __('common.button.list_of_trash') }}
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('projects.index') }}" method="GET" class="mt-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <x-form.input
                                    name="search"
                                    :value="request('search')"
                                    placeholder="{{ __('site.projects.search_placeholder') }}"
                                />
                            </div>
                            <div class="col-lg-8">
                                <button type="submit" class="btn btn-outline-gray mr-3">
                                    {{ __('common.button.search') }}
                                </button>
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-danger">
                                    {{ __('common.button.reset') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse ($projects as $project)
            @include('projects._card', ['project' => $project])
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center text-muted py-4">
                        <i class="fas fa-folder-open d-block font-20 mb-2" aria-hidden="true"></i>
                        {{ __('site.projects.no_projects') }}
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="text-start mb-3">
        <x-pagination :paginator="$projects" />
    </div>
@endsection

@push('css')
    <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
@endpush
