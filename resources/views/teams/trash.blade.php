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
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered mb-0 table-centered">
                            <thead>
                            <tr>
                                <th>{{ __('site.teams.code') }}</th>
                                <th>{{ __('site.teams.name') }}</th>
                                <th class="text-center">{{ __('site.teams.description') }}</th>
                                <th class="text-center">{{ __('site.teams.actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($teams as $team)
                                <tr>
                                    <td>
                                        <span class="badge badge-classic">{{ $team->code }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img
                                                src="{{ image_url($team->logo) }}"
                                                alt="{{ $team->name }}"
                                                class="rounded-circle thumb-sm mr-1"
                                            >
                                            <span class="font-weight-bold">{{ $team->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>{{ $team->description }}</span>
                                    </td>
                                    <td class="text-center text-nowrap">
                                        @if (request()->routeIs('teams.trash'))
                                            <x-form.confirm-button
                                                :action="route('teams.restore', $team)"
                                                title="{{ __('common.messages.restore_confirm') }}"
                                                text="{{ __('common.messages.restore_sure') }}"
                                                confirm-text="{{ __('common.button.restore') }}"
                                                cancel-text="{{ __('common.button.cancel') }}"
                                                icon="fas fa-undo"
                                                class="btn btn-sm btn-outline-success"
                                                label="{{ __('common.button.restore') }}"
                                            />
                                        @else
                                            <a
                                                href="{{ route('teams.show', $team) }}"
                                                class="btn btn-sm btn-outline-gray"
                                                style="width: 34px; height: 34px"
                                                title="{{ __('common.button.view') }}"
                                            >
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a
                                                href="{{ route('teams.edit', $team) }}"
                                                class="btn btn-sm btn-outline-warning"
                                                style="width: 34px; height: 34px"
                                                title="{{ __('common.button.edit') }}"
                                            >
                                                <i class="fas fa-edit"></i>
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
                                            />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-users d-block font-20 mb-2"></i>
                                        {{ __('site.teams.no_teams') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    <x-pagination :paginator="$teams" />
                </div>
            </div>
        </div>
    </div>
@endsection

@include('teams._confirmation-assets')
