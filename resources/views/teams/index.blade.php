@push('title', __('site.teams.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
        title="{{ __('site.teams.title') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => __('site.teams.list')],
        ]"
    />

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <x-action-button
                                icon="fas fa-plus"
                                type="primary"
                                :href="route('teams.create')"
                            >
                                {{ __('site.teams.add') }}
                            </x-action-button>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ route('teams.trash') }}" class="btn btn-sm btn-outline-gray float-right">
                                <i class="fas fa-trash-alt"></i>
                                {{ __('common.button.list_of_trash') }}
                            </a>
                        </div>
                    </div>

                    <form action="{{ route('teams.index') }}" method="GET" class="mt-3">
                        <div class="row">
                            <div class="col-lg-4">
                                <x-form.input
                                    name="search"
                                    :value="request('search')"
                                    placeholder="{{ __('site.teams.search_placeholder') }}"
                                />
                            </div>
                            <div class="col-lg-8">
                                <button type="submit" class="btn btn-outline-gray mr-3">
                                    {{ __('common.button.search') }}
                                </button>
                                <a href="{{ route('teams.index') }}" class="btn btn-outline-danger">
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
        @forelse ($teams as $team)
            @include('teams._card', ['team' => $team])
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center text-muted py-4">
                        <i class="fas fa-users d-block font-20 mb-2"></i>
                        {{ __('site.teams.no_teams') }}
                    </div>
                </div>
            </div>
        @endforelse
    </div>
    <div class="text-start mb-3">
        <x-pagination :paginator="$teams" />
    </div>
@endsection

@push('css')
    <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
@endpush
