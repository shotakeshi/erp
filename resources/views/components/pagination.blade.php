@props([
    'paginator',
])
@if ($paginator->total() > 0)
    <div class="row mt-3">
        {{-- Result information --}}
        <div class="col-lg-6">
            <div class="dataTables_info">
                {{ __('common.pagination.showing') }}
                <strong>{{ $paginator->firstItem() ?? 0 }}</strong>
                {{ __('common.pagination.to') }}
                <strong>{{ $paginator->lastItem() ?? 0 }}</strong>
                {{ __('common.pagination.of') }}
                <strong>{{ $paginator->total() }}</strong>
                {{ __('common.pagination.entries') }}
            </div>
        </div>

        {{-- Pagination --}}
        @if ($paginator->hasPages())
            <div class="col-lg-6">
                <ul class="pagination justify-content-end mb-0">
                    {{-- Previous --}}
                    <li class="paginate_button page-item previous
                        {{ $paginator->onFirstPage() ? 'disabled' : '' }}">

                        @if ($paginator->onFirstPage())
                            <span class="page-link">
                                {{ __('common.pagination.previous') }}
                            </span>
                        @else
                            <a href="{{ $paginator->previousPageUrl() }}" class="page-link">
                                {{ __('common.pagination.previous') }}
                            </a>
                        @endif
                    </li>
                    @php
                        $current = $paginator->currentPage();
                        $last = $paginator->lastPage();

                        $pages = [];

                        // Always show first page
                        $pages[] = 1;

                        // Pages around current page
                        for (
                            $page = max(2, $current - 2);
                            $page <= min($last - 1, $current + 2);
                            $page++
                        ) {
                            $pages[] = $page;
                        }

                        // Always show last page
                        if ($last > 1) {
                            $pages[] = $last;
                        }

                        $pages = collect($pages)
                            ->unique()
                            ->sort()
                            ->values();
                    @endphp

                    @php
                        $previousPage = null;
                    @endphp

                    @foreach ($pages as $page)

                        {{-- Ellipsis --}}
                        @if (
                            $previousPage !== null &&
                            $page > $previousPage + 1
                        )
                            <li class="paginate_button page-item disabled">
                                <span class="page-link">
                                    ...
                                </span>
                            </li>
                        @endif

                        {{-- Page --}}
                        <li  class="paginate_button page-item  {{ $page == $current ? 'active' : '' }}" >
                            @if ($page == $current)
                                <span class="page-link">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $paginator->url($page) }}" class="page-link" >
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                        @php
                            $previousPage = $page;
                        @endphp
                    @endforeach

                    {{-- Next --}}
                    <li class="paginate_button page-item next
                        {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                        @if ($paginator->hasMorePages())
                            <a href="{{ $paginator->nextPageUrl() }}" class="page-link" >
                                {{ __('common.pagination.next') }}
                            </a>
                        @else
                            <span class="page-link">
                                {{ __('common.pagination.next') }}
                            </span>
                        @endif
                    </li>
                </ul>
            </div>
        @endif
    </div>
@endif
