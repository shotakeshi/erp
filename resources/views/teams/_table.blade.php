<div class="table-responsive mt-3">
    <table class="table table-bordered mb-0 table-centered">
        <thead>
            <tr>
                <th>{{ __('site.teams.code') }}</th>
                <th>{{ __('site.teams.name') }}</th>
                <th class="text-center">{{ __('site.teams.current_members') }}</th>
                <th class="text-center">{{ __('site.teams.current_managers') }}</th>
                <th>{{ __('site.teams.updated_at') }}</th>
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
                            <span class="avatar-box thumb-sm align-self-center mr-2">
                                <span class="avatar-title bg-soft-primary rounded-circle">
                                    <i class="fas fa-users"></i>
                                </span>
                            </span>
                            @if (request()->routeIs('teams.trash'))
                                <span class="font-weight-bold">{{ $team->name }}</span>
                            @else
                                <a href="{{ route('teams.show', $team) }}" class="font-weight-bold text-primary">
                                    {{ $team->name }}
                                </a>
                            @endif
                        </div>
                    </td>
                    <td class="text-center">
                        <span>{{ $team->current_members_count }}</span>
                    </td>
                    <td class="text-center">
                        <span>{{ $team->current_managers_count }}</span>
                    </td>
                    <td class="text-nowrap">{{ $team->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
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
