@php
    $tabQuery = request()->except('team');
    $tabs = [
        [
            'route' => 'teams.show',
            'label' => __('site.teams.general'),
        ],
        [
            'route' => 'teams.members.index',
            'label' => __('site.teams.members'),
            'count' => $team->current_members_count,
        ],
        [
            'route' => 'teams.managers.index',
            'label' => __('site.teams.managers'),
            'count' => $team->current_managers_count,
        ],
        [
            'route' => 'teams.members.history',
            'label' => __('site.teams.member_history'),
        ],
    ];
@endphp

<div class="card mb-3">
    <div class="card-body pb-0">
        <div class="media align-items-center">
            <span class="avatar-box thumb-lg align-self-center mr-3">
                <span class="avatar-title bg-soft-primary rounded-circle font-20">
                    <i class="fas fa-users"></i>
                </span>
            </span>
            <div class="media-body">
                <h4 class="header-title mb-1">{{ $team->name }}</h4>
                <span class="badge badge-classic">{{ $team->code }}</span>
            </div>
        </div>

        <nav aria-label="{{ __('site.teams.detail') }}">
            <ul class="nav nav-tabs mt-3">
                @foreach ($tabs as $tab)
                    @php
                        $isActive = request()->routeIs($tab['route']);
                    @endphp
                    <li class="nav-item">
                        <a
                            href="{{ route($tab['route'], array_merge(['team' => $team], $tabQuery)) }}"
                            @class(['nav-link', 'active' => $isActive])
                            @if ($isActive) aria-current="page" @endif
                        >
                            {{ $tab['label'] }}
                            @isset($tab['count'])
                                <span class="badge badge-soft-light">({{ $tab['count'] }})</span>
                            @endisset
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</div>
