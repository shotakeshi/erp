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
    @if (! $team->trashed())
        <div class="card-body d-flex align-items-center">
            <ul class="nav nav-pills mb-0 mr-3" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a href="{{ route('teams.members.index', $team) }}"
                        @class(['nav-link', 'active' => request()->routeIs('teams.members.index')])
                    >
                        {{ __('site.teams.members') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('teams.members.history', $team) }}"
                        @class(['nav-link', 'active' => request()->routeIs(['teams.members.history', 'teams.show'])])
                    >
                        {{ __('site.teams.member_history') }}
                    </a>
                </li>
            </ul>
            <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-outline-warning ml-auto flex-shrink-0" title="{{ __('site.teams.edit') }}">
                <i class="fas fa-edit mr-1" aria-hidden="true"></i>
                {{ __('site.teams.edit') }}
            </a>
        </div>
    @endif
</div>
