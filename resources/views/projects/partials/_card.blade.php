@php
    $members = $project->members ?? collect();
    $previewMembers = $members->take(3);
    $membersCount = $project->current_members_count ?? $members->count();
    $remainingMembersCount = max($membersCount - $previewMembers->count(), 0);
@endphp

<div class="col-sm-6 col-lg-3">
    <div class="card team-card h-100">
        <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-start">
                <span class="text-muted">{{ $project->code }}</span>
                <div class="d-flex align-items-center">
                    <span class="{{ $project->status->badgeClass() }} mr-2">
                        {{ $project->status->label() }}
                    </span>
                    <div class="dropdown d-inline-block">
                        <button
                            type="button"
                            class="btn p-0 border-0 bg-transparent nav-link dropdown-toggle arrow-none"
                            id="project-actions-{{ $project->id }}"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false"
                            aria-label="{{ __('site.projects.actions') }}"
                        >
                            <i class="fas fa-ellipsis-v font-16 text-muted" aria-hidden="true"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="project-actions-{{ $project->id }}">
                            <a class="dropdown-item text-gray" href="{{ route('projects.show', $project) }}">
                                {{ __('common.button.view') }}
                            </a>
                            <a class="dropdown-item text-warning" href="{{ route('projects.edit', $project) }}">
                                {{ __('common.button.edit') }}
                            </a>
                            <x-form.confirm-button
                                :action="route('projects.destroy', $project)"
                                method="DELETE"
                                title="{{ __('site.projects.delete_confirmation_title') }}"
                                text="{{ __('site.projects.delete_confirmation') }}"
                                confirm-text="{{ __('site.projects.archive') }}"
                                cancel-text="{{ __('common.button.cancel') }}"
                                icon=""
                                class="dropdown-item text-danger"
                                label="{{ __('site.projects.archive') }}"
                            />
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center project-card flex-grow-1 pt-3">
                <img
                    src="{{ image_url($project->project_avatar, 'images/widgets/project1.jpg') }}"
                    alt="{{ $project->name }}"
                    width="80"
                    height="80"
                    class="mx-auto d-block rounded-circle mb-3"
                >
                <h4 class="project-title mb-2">
                    <a href="{{ route('projects.show', $project) }}" class="text-dark">
                        {{ $project->name }}
                    </a>
                </h4>
                <p class="text-muted mb-3">
                    <span class="text-secondary font-14">{{ __('site.projects.description') }}:</span>
                    {{ $project->description ?: '-' }}
                </p>

                @if ($project->project_url)
                    <a
                        href="{{ $project->project_url }}"
                        class="text-info text-break"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {{ $project->project_url }}
                    </a>
                @endif
            </div>

            <div class="d-flex justify-content-center align-items-center">
                @if ($previewMembers->isNotEmpty())
                    <div class="img-group text-nowrap">
                        @foreach ($previewMembers as $member)
                            <span
                                class="user-avatar user-avatar-group"
                                data-toggle="tooltip"
                                data-placement="top"
                                data-original-title="{{ $member->full_name }}"
                            >
                                <img
                                    src="{{ image_url($member->avatar) }}"
                                    alt="{{ $member->full_name }}"
                                    class="rounded-circle thumb-xs"
                                >
                            </span>
                        @endforeach
                        @if ($remainingMembersCount > 0)
                            <a href="{{ route('projects.show', $project) }}" class="avatar-box thumb-xs align-self-center">
                                <span class="avatar-title bg-soft-secondary rounded-circle font-weight-normal">
                                    +{{ $remainingMembersCount }}
                                </span>
                            </a>
                        @endif
                    </div>
                @endif
                <small class="font-13 text-muted text-nowrap ml-3">
                    {{ trans_choice('site.projects.member_count', $membersCount, ['count' => $membersCount]) }}
                </small>
            </div>
        </div>
    </div>
</div>
