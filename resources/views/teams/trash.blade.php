@push('title', __('site.teams.title'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.title') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => __('common.button.list_of_trash')],
        ]"
    />

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-gray">
                        <i class="fas fa-arrow-left mr-1"></i>
                        {{ __('site.teams.back_to_list') }}
                    </a>

                    @include('teams._filter', ['action' => route('teams.trash')])
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
