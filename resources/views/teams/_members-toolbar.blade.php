@php
    $isGrid = request()->routeIs('teams.show');
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-sm-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-white">
                            <i class="fas fa-search text-muted" aria-hidden="true"></i>
                        </span>
                    </div>
                    <input type="search" class="form-control" id="searchMemberList"
                        placeholder="{{ __('site.teams.member_search_placeholder') }}"
                        aria-label="{{ __('common.button.search') }}">
                </div>
            </div>
            <div class="col-sm-auto ml-sm-auto mt-2 mt-sm-0">
                <div class="list-grid-nav d-flex align-items-center">
                    <a href="{{ route('teams.show', $team) }}" id="grid-view-button"
                        class="btn btn-sm mr-1 {{ $isGrid ? 'btn-primary active' : 'btn-soft-info' }}"
                        title="{{ __('common.button.grid_view') }}" aria-label="{{ __('common.button.grid_view') }}"
                        @if ($isGrid) aria-current="page" @endif>
                        <i class="fas fa-th" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('teams.members.index', $team) }}" id="list-view-button"
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
        <p id="member-search-empty" class="text-muted mt-3 mb-0 d-none" role="status">
            {{ __('site.teams.member_search_empty') }}
        </p>
    </div>
</div>

@push('scripts')
    <script>
        $(function () {
            const $input = $('#searchMemberList');
            const $members = $('[data-member-search]');
            const $emptyMessage = $('#member-search-empty');
            const $viewLinks = $('#grid-view-button, #list-view-button');
            const normalize = (value) => value.trim().toLocaleLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');

            function filterMembers() {
                const searchValue = $input.val().trim();
                const search = normalize(searchValue);
                let visibleCount = 0;

                $members.each(function () {
                    const $member = $(this);
                    const matches = normalize($member.data('member-search')).includes(search);

                    $member.toggleClass('d-none', !matches);
                    visibleCount += matches ? 1 : 0;
                });

                $emptyMessage.toggleClass('d-none', !search || visibleCount > 0);

                $viewLinks.each(function () {
                    const url = new URL(this.href);

                    if (searchValue) {
                        url.searchParams.set('search', searchValue);
                    } else {
                        url.searchParams.delete('search');
                    }

                    $(this).attr('href', url.toString());
                });
            }

            $input.val(new URLSearchParams(window.location.search).get('search') || '');
            $input.on('input', filterMembers);
            filterMembers();
        });
    </script>
@endpush
