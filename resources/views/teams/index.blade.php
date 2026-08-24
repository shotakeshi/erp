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
                            <a href="{{ route('teams.trash') }}" class="btn btn-outline-gray float-right">
                                <i class="fas fa-trash-alt"></i>
                                {{ __('common.button.list_of_trash') }}
                            </a>
                        </div>
                    </div>

                    @include('teams._filter', ['action' => route('teams.index')])
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @include('teams._table', ['teams' => $teams])
                    <x-pagination :paginator="$teams" />
                </div>
            </div>
        </div>
    </div>
@endsection

@include('teams._confirmation-assets')
