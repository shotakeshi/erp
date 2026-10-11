@push('title', $team->name)
@extends('layouts.master')

@section('content')
    <x-page-title
        title="{{ __('site.teams.detail') }}"
        :breadcrumbs="[
            [
                'title' => __('site.teams.title'),
                'url' => $team->trashed() ? route('teams.trash') : route('teams.index'),
            ],
            ['title' => $team->name],
        ]"
    />

    @include('teams._team-tabs', ['team' => $team, 'activeTab' => $activeTab])

    <div class="tab-content mt-3" id="team-tab-content">
        @unless ($team->trashed())
            <div
                @class(['tab-pane fade', 'show active' => $activeTab === \App\Http\Controllers\TeamController::MEMBERS_TAB])
                id="team-members"
                role="tabpanel"
                aria-labelledby="team-members-tab"
            >
                @include('teams._assignment-toolbar', ['team' => $team, 'mode' => $mode])

                @if ($mode === \App\Http\Controllers\TeamController::LIST_MODE)
                    @include('teams._assignment-list', ['team' => $team, 'assignments' => $assignments])
                @else
                    @include('teams._assignment-grid', ['team' => $team, 'assignments' => $assignments])
                @endif

                <x-pagination :paginator="$assignments"/>
            </div>
        @endunless

        <div
            @class(['tab-pane fade', 'show active' => $activeTab === \App\Http\Controllers\TeamController::HISTORY_TAB])
            id="team-history"
            role="tabpanel"
            aria-labelledby="team-history-tab"
        >
            <x-assignment-history
                :assignments="$histories"
                lang-prefix="site.teams"
            />
        </div>
    </div>

    @unless ($team->trashed())
        <x-remove-assignment-modal
            :owner="$team"
            :assignments="$assignments->getCollection()"
            destroy-route="teams.assignments.destroy"
            lang-prefix="site.teams"
        />

        @include('teams._add-assignment-modal', [
            'team' => $team,
            'employees' => $employees,
            'storeAction' => route('teams.assignments.store', $team),
        ])
    @endunless
@endsection
