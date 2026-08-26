@php
    $tabQuery = request()->except('team');
    $tabs = [
        [
            'route' => 'teams.show',
            'label' => __('site.teams.general'),
            'icon' => 'fas fa-info-circle',
        ],
        [
            'route' => 'teams.members.index',
            'label' => __('site.teams.members'),
            'icon' => 'fas fa-users',
            'count' => $team->current_members_count,
        ],
        [
            'route' => 'teams.managers.index',
            'label' => __('site.teams.managers'),
            'icon' => 'fas fa-user-tie',
            'count' => $team->current_managers_count,
        ],
    ];
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-5">
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
            </div>

            <div class="col-lg-7 mt-3 mt-lg-0 text-lg-right">
                @foreach ($tabs as $tab)
                    @php
                        $isActive = request()->routeIs($tab['route']);
                    @endphp
                    <a href="{{ route($tab['route'], array_merge(['team' => $team], $tabQuery)) }}"
                        @class([
                            'btn btn-sm mb-1',
                            'btn-primary' => $isActive,
                            'btn-outline-primary' => ! $isActive,
                        ])
                    >
                        <i class="{{ $tab['icon'] }} mr-1"></i>
                        {{ $tab['label'] }}
                        @isset($tab['count'])
                            <span class="badge badge-soft-light ml-1">({{ $tab['count'] }})</span>
                        @endisset
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
