<div data-employee-picker
    data-url="{{ $employeeOptionsUrl }}"
    data-add-label="{{ __('common.button.add') }}"
    data-remove-label="{{ __('site.teams.remove') }}"
    data-empty-label="{{ __('site.teams.no_available_employees') }}"
    data-error-label="{{ __('site.teams.employee_search_failed') }}">
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-search" aria-hidden="true"></i></span>
        </div>
        <input type="search" class="form-control bg-light border-0"
            placeholder="{{ __('site.teams.search_employee_placeholder') }}"
            aria-label="{{ __('site.teams.search_employee_placeholder') }}" data-picker-search>
    </div>
    <div class="d-flex align-items-center mb-3">
        <h6 class="mb-0 mr-3">{{ __('site.teams.members') }}:</h6>
        <div class="d-flex align-items-center flex-wrap" data-picker-selected></div>
    </div>
    <p class="text-muted d-none" role="status" data-picker-loading>{{ __('site.teams.loading_employees') }}</p>
    <div class="alert alert-danger d-none" role="alert" data-picker-error>
        <span data-picker-error-message></span>
        <button type="button" class="btn btn-sm btn-outline-danger ml-2" data-picker-retry>{{ __('site.teams.retry') }}</button>
    </div>
    <div class="overflow-auto" style="max-height: 360px;" data-picker-scroll tabindex="0"
        aria-label="{{ __('site.teams.members') }}">
        <div data-picker-list></div>
    </div>
</div>

@once
    @push('scripts')
        <script src="{{ asset('js/teams/team-employee-picker.js') }}"></script>
    @endpush
@endonce
