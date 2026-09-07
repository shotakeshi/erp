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

        </div>
    </div>

    <div class="row">
        @forelse ($teams as $team)
            @php
                $previewEmployees = $team->managers->merge($team->members)->take(3);
                $employeesCount = $team->current_managers_count + $team->current_members_count;
                $remainingCount = max($employeesCount - $previewEmployees->count(), 0);
            @endphp

            <div class="col-sm-6 col-lg-3">
                <div class="card team-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end">
                            <div class="dropdown d-inline-block">
                                <a
                                    type="button"
                                    class="nav-link dropdown-toggle arrow-none"
                                    id="team-actions-{{ $team->id }}"
                                    data-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false"
                                >
                                    <i class="fas fa-ellipsis-v font-20 text-muted"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="team-actions-{{ $team->id }}">
                                    <a class="dropdown-item text-gray" href="{{ route('teams.show', $team) }}">
                                        {{ __('common.button.view') }}
                                    </a>
                                    <a class="dropdown-item text-warning" href="{{ route('teams.edit', $team) }}">
                                        {{ __('common.button.edit') }}
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
                                        icon=""
                                        class="dropdown-item text-danger"
                                        label="{{ __('common.button.delete') }}"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="text-center project-card">
                            <img
                                src="{{ image_url($team->logo) }}"
                                alt="{{ $team->name }}"
                                width="80"
                                height="80"
                                style="object-fit: cover;"
                                class="mx-auto d-block rounded-circle mb-3"
                            >
                            <h4 class="project-title">{{ $team->name }}</h4>
                            <p class="text-muted mb-3">
                                <span class="text-secondary font-14">{{ __('site.teams.description') }}:</span>
                                {{ $team->description ?? '-' }}
                            </p>
                        </div>

                        <div class="d-flex flex-column justify-content-center align-items-center">
                            @if ($previewEmployees->isNotEmpty())
                                <div class="img-group text-nowrap">
                                    @foreach ($previewEmployees as $employee)
                                        <span class="user-avatar user-avatar-group" title="{{ $employee->fullname }}">
                                            <img
                                                src="{{ image_url($employee->avatar) }}"
                                                alt="{{ $employee->fullname }}"
                                                class="rounded-circle thumb-xs"
                                            >
                                        </span>
                                    @endforeach
                                    @if ($remainingCount > 0)
                                        <a href="{{ route('teams.show', $team) }}" class="avatar-box thumb-xs align-self-center">
                                            <span class="avatar-title bg-soft-info rounded-circle font-13 font-weight-normal">
                                                +{{ $remainingCount }}
                                            </span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                            <small class="font-13 text-muted text-nowrap">
                                {{ trans_choice('site.teams.manager_count', $team->current_managers_count, ['count' => $team->current_managers_count]) }}
                                <span aria-hidden="true">&middot;</span>
                                {{ trans_choice('site.teams.member_count', $team->current_members_count, ['count' => $team->current_members_count]) }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
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

@include('teams._confirmation-assets')
