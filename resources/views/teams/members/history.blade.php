@push('title', __('site.teams.member_history'))
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.member_history') }}"
        :breadcrumbs="[
            ['title' => __('site.teams.title'), 'url' => route('teams.index')],
            ['title' => $team->name, 'url' => route('teams.members.index', $team)],
            ['title' => __('site.teams.member_history')],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team])

    <div class="card">
        <div class="card-body">
            @php
                $historyFilterOptions = collect(['all', 'current', 'past'])
                    ->mapWithKeys(fn ($filter) => [
                        $filter => __('site.teams.history_filters.' . $filter),
                    ])
                    ->all();
            @endphp

            <form action="{{ route('teams.members.history', $team) }}" method="GET" class="mb-3">
                <div class="row">
                    <div class="col-lg-3">
                        <x-form.select
                            name="filter"
                            :options="$historyFilterOptions"
                            :selected="request('filter', 'all')"
                            required
                        />
                    </div>
                    <div class="col-lg-3 mt-2 mt-lg-0">
                        <button type="submit" class="btn btn-outline-gray mr-1">
                            {{ __('common.button.filter') }}
                        </button>
                        <a href="{{ route('teams.members.history', $team) }}" class="btn btn-outline-danger">
                            {{ __('common.button.reset') }}
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered mb-0 table-centered">
                    <thead>
                        <tr>
                            <th>{{ __('site.teams.employee') }}</th>
                            <th>{{ __('site.teams.role') }}</th>
                            <th>{{ __('site.teams.assignment_period') }}</th>
                            <th>{{ __('site.teams.end_reason') }}</th>
                            <th>{{ __('site.teams.created_by') }}</th>
                            <th>{{ __('site.teams.ended_by') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($memberships as $membership)
                            <tr>
                                <td>
                                    <div class="media">
                                        <a class="" href="{{ route('employees.show', $membership->employee) }}">
                                            <img src="{{ image_url($membership->employee->avatar) }}" alt="{{ $membership->employee->full_name }}" class="rounded-circle thumb-md">
                                        </a>
                                        <div class="media-body align-self-center ml-3">
                                            <p class="font-14 font-weight-bold mb-0">{{ $membership->employee->full_name }}</p>
                                            <p class="mb-0 font-12 text-muted">{{ $membership->employee->email }}</p>
                                        </div>
                                    </div><!--end media-->
                                </td>
                                <td>{{ $membership->role }}</td>
                                <td class="text-nowrap">
                                    {{ $membership->start_date->format('d/m/Y') }}
                                    <i class="fas fa-long-arrow-alt-right"></i>
                                    @if ($membership->end_date)
                                        {{ $membership->end_date->format('d/m/Y') }}
                                    @else
                                        <span class="badge badge-soft-success">{{ __('site.teams.history_filters.current') }}</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $membership->end_reason
                                        ? __('site.teams.end_reasons.' . $membership->end_reason->value)
                                        : '-' }}
                                    @if (filled($membership->end_reason_note))
                                        <small class="d-block text-muted mt-1">{{ $membership->end_reason_note }}</small>
                                    @endif
                                </td>
                                <td>{{ $membership->createdBy?->name ?? __('site.teams.system_or_legacy') }}</td>
                                <td>{{ $membership->endedBy?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-history d-block font-20 mb-2"></i>
                                    {{ __('site.teams.no_member_history') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <x-pagination :paginator="$memberships" />
        </div>
    </div>
@endsection
