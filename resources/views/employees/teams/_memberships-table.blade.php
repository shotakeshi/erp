<div class="table-responsive">
    <table class="table table-bordered mb-0 table-centered">
        <thead>
            <tr>
                <th>{{ __('site.teams.code') }}</th>
                <th>{{ __('site.teams.name') }}</th>
                <th>{{ __('site.teams.start_date') }}</th>
                <th>{{ __('site.teams.end_date') }}</th>
                <th>{{ __('site.teams.end_reason') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($memberships as $membership)
                <tr>
                    <td><span class="badge badge-classic">{{ $membership->team->code }}</span></td>
                    <td>
                        @if ($membership->team->trashed())
                            <span class="font-weight-bold">{{ $membership->team->name }}</span>
                            <span class="badge badge-soft-danger ml-1">{{ __('site.teams.team_deleted') }}</span>
                        @else
                            <a href="{{ route('teams.show', $membership->team) }}" class="font-weight-bold text-primary">
                                {{ $membership->team->name }}
                            </a>
                        @endif
                    </td>
                    <td class="text-nowrap">{{ $membership->start_date->format('d/m/Y') }}</td>
                    <td class="text-nowrap">
                        {{ $membership->end_date?->format('d/m/Y') ?? __('site.teams.history_filters.current') }}
                    </td>
                    <td>
                        {{ $membership->end_reason
                            ? __('site.teams.end_reasons.' . $membership->end_reason->value)
                            : '-' }}
                        @if (filled($membership->end_reason_note))
                            <small class="d-block text-muted mt-1">{{ $membership->end_reason_note }}</small>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="fas fa-users d-block font-20 mb-2"></i>
                        {{ $emptyMessage }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($showPagination)
    <x-pagination :paginator="$memberships" />
@endif
