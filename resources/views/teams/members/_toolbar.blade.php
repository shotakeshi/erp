@php
    $isGrid = $mode === 'grid';
    $resetUrl = route('teams.members.index', ['team' => $team, 'mode' => $mode]);
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <form action="{{ route('teams.members.index', $team) }}" method="GET">
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
                <div class="list-grid-nav d-flex align-items-center justify-content-lg-end">
                    <a href="{{ route('teams.members.index', ['team' => $team, 'mode' => 'grid', 'search' => request('search')]) }}" id="grid-view-button"
                        class="btn btn-sm mr-1 {{ $isGrid ? 'btn-primary active' : 'btn-soft-info' }}"
                        title="{{ __('common.button.grid_view') }}" aria-label="{{ __('common.button.grid_view') }}"
                        @if ($isGrid) aria-current="page" @endif>
                        <i class="fas fa-th" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('teams.members.index', ['team' => $team, 'mode' => 'list', 'search' => request('search')]) }}" id="list-view-button"
                        class="btn btn-sm mr-1 {{ $isGrid ? 'btn-soft-info' : 'btn-primary active' }}"
                        title="{{ __('common.button.list_view') }}" aria-label="{{ __('common.button.list_view') }}"
                        @if (! $isGrid) aria-current="page" @endif>
                        <i class="fas fa-list" aria-hidden="true"></i>
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#add-member-modal">
                        <i class="fas fa-plus mr-1" aria-hidden="true"></i>
                        {{ __('site.teams.add_members') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
