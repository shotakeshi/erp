@php
    $previewEmployees = $team->members->take(3);
    $employeesCount = $team->current_members_count;
    $remainingCount = max($employeesCount - $previewEmployees->count(), 0);
@endphp

<div class="col-sm-6 col-lg-3">
    <div class="card team-card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <span class="text-muted">{{ $team->code }}</span>
                <div class="dropdown d-inline-block">
                    <a
                        type="button"
                        class="nav-link dropdown-toggle arrow-none"
                        id="team-actions-{{ $team->id }}"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        <i class="fas fa-ellipsis-v font-16 text-muted"></i>
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
                            text="{{ __('site.teams.delete_confirmation') }}"
                            confirm-text="{{ __('site.teams.archive') }}"
                            cancel-text="{{ __('common.button.cancel') }}"
                            icon=""
                            class="dropdown-item text-danger"
                            label="{{ __('site.teams.archive') }}"
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

            <div class="d-flex justify-content-center align-items-center">
                @if ($previewEmployees->isNotEmpty())
                    <div class="img-group text-nowrap">
                        @foreach ($previewEmployees as $employee)
                            <span
                                class="user-avatar user-avatar-group"
                                data-toggle="tooltip"
                                data-placement="top"
                                data-original-title="{{ $employee->full_name }}"
                            >
                                <img
                                    src="{{ image_url($employee->avatar) }}"
                                    alt="{{ $employee->full_name }}"
                                    class="rounded-circle thumb-xs"
                                >
                            </span>
                        @endforeach
                        @if ($remainingCount > 0)
                            <a href="{{ route('teams.show', $team) }}" class="avatar-box thumb-xs align-self-center">
                                <span class="avatar-title bg-soft-secondary rounded-circle font-weight-normal">
                                    +{{ $remainingCount }}
                                </span>
                            </a>
                        @endif
                    </div>
                @endif
                <small class="font-13 text-muted text-nowrap ml-3">
                    {{ trans_choice('site.teams.member_count', $employeesCount, ['count' => $employeesCount]) }}
                </small>
            </div>
        </div>
    </div>
</div>
