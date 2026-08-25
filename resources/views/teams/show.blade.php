@push('title', $team->name)
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.detail') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    <div class="row mb-3">
        <div class="col-lg-6">
            <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-gray">
                <i class="fas fa-arrow-left mr-1"></i>
                {{ __('site.teams.back_to_list') }}
            </a>
        </div>
        <div class="col-lg-6 mt-2 mt-lg-0 text-lg-right">
            <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-edit mr-1"></i>
                {{ __('site.teams.edit') }}
            </a>
            <x-form.confirm-button
                :action="route('teams.destroy', $team)"
                method="DELETE"
                title="{{ __('site.teams.delete_confirmation_title') }}"
                text="{{ __('site.teams.delete_confirmation', [
                    'members' => $team->current_members_count,
                    'managers' => $team->current_managers_count,
                ]) }}"
                confirm-text="{{ __('common.button.delete') }}"
                cancel-text="{{ __('common.button.cancel') }}"
                icon="fas fa-trash"
                class="btn btn-sm btn-outline-danger"
                label="{{ __('common.button.delete') }}"
            />
        </div>
    </div>

    <div class="card">
        <div class="card-header">{{ __('site.teams.general') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('site.teams.name') }}</dt>
                        <dd class="col-sm-8">{{ $team->name }}</dd>
                        <dt class="col-sm-4">{{ __('site.teams.code') }}</dt>
                        <dd class="col-sm-8"><span class="badge badge-light">{{ $team->code }}</span></dd>
                    </dl>
                </div>
                <div class="col-lg-6">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ __('site.teams.created_at') }}</dt>
                        <dd class="col-sm-8">{{ $team->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                        <dt class="col-sm-4">{{ __('site.teams.updated_at') }}</dt>
                        <dd class="col-sm-8">{{ $team->updated_at?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </dl>
                </div>
                <div class="col-lg-12 mt-3">
                    <div class="font-weight-semibold mb-1">{{ __('site.teams.description') }}</div>
                    <div class="text-break">{{ $team->description ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>

@endsection

@include('teams._confirmation-assets')
