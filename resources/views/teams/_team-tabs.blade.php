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
    <div class="card-body">
        <ul class="nav nav-pills mb-0" id="pills-tab" role="tablist">
            <li class="nav-item">
                <a href="{{ route('teams.show', $team) }}"
                    @class(['nav-link', 'active' => request()->routeIs('teams.show')])
                >
                    {{ __('site.teams.general') }}
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('teams.members.history', $team) }}"
                    @class(['nav-link', 'active' => request()->routeIs('teams.members.history')])
                >
                    {{ __('site.teams.member_history') }}
                </a>
            </li>
        </ul>
    </div>
</div>
