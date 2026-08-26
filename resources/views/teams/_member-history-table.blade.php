@php
    $historyFilterOptions = collect(['all', 'current', 'past'])
        ->mapWithKeys(fn ($filter) => [
            $filter => __('site.teams.history_filters.' . $filter),
        ])
        ->all();
@endphp

<form action="{{ $filterAction }}" method="GET" class="mb-3">
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
            <a href="{{ $filterAction }}" class="btn btn-outline-danger">
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
                <th>{{ __('site.teams.employee_code') }}</th>
                <th>{{ __('site.teams.start_date') }}</th>
                <th>{{ __('site.teams.end_date') }}</th>
                <th>{{ __('site.teams.end_reason') }}</th>
                <th>{{ __('site.teams.created_by') }}</th>
                <th>{{ __('site.teams.ended_by') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($memberships as $membership)
                <tr>
                    <td>
                        <div class="font-14 mb-1 font-weight-bold">{{ $membership->employee->full_name }}</div>
                        <x-employee-lifecycle-badge :employee="$membership->employee" />
                    </td>
                    <td>{{ $membership->employee->employee_id }}</td>
                    <td class="text-nowrap">{{ $membership->start_date->format('d/m/Y') }}</td>
                    <td class="text-nowrap">
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
                    </td>
                    <td>{{ $membership->createdBy?->name ?? __('site.teams.system_or_legacy') }}</td>
                    <td>{{ $membership->endedBy?->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="fas fa-history d-block font-20 mb-2"></i>
                        {{ __('site.teams.no_member_history') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<x-pagination :paginator="$memberships" />
