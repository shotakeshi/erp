@php
    use App\Http\Controllers\TeamController;

    $membersTab = TeamController::MEMBERS_TAB;
    $isGrid = $mode === TeamController::GRID_MODE;
    $resetUrl = route('teams.show', ['team' => $team, 'tab' => $membersTab, 'mode' => $mode]);
    $gridUrl = route('teams.show', [
        'team' => $team,
        'tab' => $membersTab,
        'mode' => TeamController::GRID_MODE,
        'search' => request('search'),
    ]);
    $listUrl = route('teams.show', [
        'team' => $team,
        'tab' => $membersTab,
        'mode' => TeamController::LIST_MODE,
        'search' => request('search'),
    ]);
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <form action="{{ route('teams.show', $team) }}" method="GET">
                    <input type="hidden" name="tab" value="{{ $membersTab }}">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    <div class="row">
                        <div class="col-lg-8">
                            <x-form.input
                                name="search"
                                placeholder="{{ __('site.teams.member_search_placeholder') }}"
                                :value="request('search')"
                            />
                        </div>
                        <div class="col-lg-2 mt-2 mt-lg-0">
                            <button type="submit" class="btn btn-outline-gray w-100">
                                {{ __('common.button.search') }}
                            </button>
                        </div>
                        <div class="col-lg-2 mt-2 mt-lg-0">
                            <a href="{{ $resetUrl }}" class="btn btn-outline-danger w-100">
                                {{ __('common.button.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 mt-2 mt-lg-0">
                <div class="d-flex align-items-center justify-content-lg-end">
                    <a
                        href="{{ $gridUrl }}"
                        @class(['btn btn-sm mr-1', 'btn-primary active' => $isGrid, 'btn-soft-info' => ! $isGrid])
                        title="{{ __('common.button.grid_view') }}"
                        aria-label="{{ __('common.button.grid_view') }}"
                        @if ($isGrid) aria-current="page" @endif
                    >
                        <i class="fas fa-th" aria-hidden="true"></i>
                    </a>
                    <a
                        href="{{ $listUrl }}"
                        @class(['btn btn-sm mr-1', 'btn-primary active' => ! $isGrid, 'btn-soft-info' => $isGrid])
                        title="{{ __('common.button.list_view') }}"
                        aria-label="{{ __('common.button.list_view') }}"
                        @if (! $isGrid) aria-current="page" @endif
                    >
                        <i class="fas fa-list" aria-hidden="true"></i>
                    </a>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        data-toggle="modal"
                        data-target="#add-assignment-modal"
                    >
                        <i class="fas fa-plus mr-1" aria-hidden="true"></i>
                        {{ __('site.teams.add_members') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
