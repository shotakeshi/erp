@props(['url', 'labels' => []])

@php
    $labels = array_replace([
        'add' => __('common.button.add'),
        'remove' => __('site.teams.remove'),
        'empty' => __('common.employee_picker.empty'),
        'error' => __('common.employee_picker.error'),
        'search' => __('common.employee_picker.search'),
        'members' => __('site.teams.members'),
        'loading' => __('common.employee_picker.loading'),
        'retry' => __('common.employee_picker.retry'),
    ], $labels);
@endphp

<div data-employee-picker
    data-url="{{ $url }}"
    data-add-label="{{ $labels['add'] }}"
    data-remove-label="{{ $labels['remove'] }}"
    data-empty-label="{{ $labels['empty'] }}"
    data-error-label="{{ $labels['error'] }}">
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-search" aria-hidden="true"></i></span>
        </div>
        <input type="search" class="form-control bg-light border-0"
            placeholder="{{ $labels['search'] }}"
            aria-label="{{ $labels['search'] }}" data-picker-search>
    </div>
    <div class="d-flex align-items-center mb-3">
        <h6 class="mb-0 mr-3">{{ $labels['members'] }}:</h6>
        <div class="d-flex align-items-center flex-wrap" data-picker-selected></div>
    </div>
    <p class="text-muted d-none" role="status" data-picker-loading>{{ $labels['loading'] }}</p>
    <div class="alert alert-danger d-none" role="alert" data-picker-error>
        <span data-picker-error-message></span>
        <button type="button" class="btn btn-sm btn-outline-danger ml-2" data-picker-retry>{{ $labels['retry'] }}</button>
    </div>
    <div class="overflow-auto" style="max-height: 360px;" data-picker-scroll tabindex="0"
        aria-label="{{ $labels['members'] }}">
        <div data-picker-list></div>
    </div>
</div>

@once
    @push('scripts')
        <script src="{{ asset('js/employee-picker.js') }}"></script>
    @endpush
@endonce
