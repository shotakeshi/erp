@php
    $membersTab = \App\Http\Controllers\TeamController::MEMBERS_TAB;
    $historyTab = \App\Http\Controllers\TeamController::HISTORY_TAB;
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="media align-items-center">
                    <img
                        src="{{ image_url($team->logo) }}"
                        alt="{{ $team->name }}"
                        class="rounded-circle thumb-xl mr-3"
                    >
                    <div class="media-body">
                        <p class="header-title mb-1 met-user-name">{{ $team->name }}</p>
                        <span class="badge badge-classic">{{ $team->code }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body d-flex align-items-center">
        <ul class="nav nav-pills mb-0 mr-3" id="team-tabs" role="tablist">
            @unless ($team->trashed())
                <li class="nav-item">
                    <a
                        @class(['nav-link', 'active' => $activeTab === $membersTab])
                        id="team-members-tab"
                        data-toggle="pill"
                        href="#team-members"
                        role="tab"
                        aria-controls="team-members"
                        aria-selected="{{ $activeTab === $membersTab ? 'true' : 'false' }}"
                    >
                        {{ __('site.teams.members') }}
                    </a>
                </li>
            @endunless
            <li class="nav-item">
                <a
                    @class(['nav-link', 'active' => $activeTab === $historyTab])
                    id="team-history-tab"
                    data-toggle="pill"
                    href="#team-history"
                    role="tab"
                    aria-controls="team-history"
                    aria-selected="{{ $activeTab === $historyTab ? 'true' : 'false' }}"
                >
                    {{ __('site.teams.member_history') }}
                </a>
            </li>
        </ul>
        @unless ($team->trashed())
            <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-outline-warning ml-auto flex-shrink-0" title="{{ __('site.teams.edit') }}">
                <i class="fas fa-edit mr-1" aria-hidden="true"></i>
                {{ __('site.teams.edit') }}
            </a>
        @endunless
    </div>
</div>
