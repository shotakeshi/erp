@props([
    'id',
    'title',
    'url',
    'labels' => [],
    'action' => null,
    'confirmLabel' => null,
    'initialEmployees' => [],
])

@php
    $isForm = $action !== null;
    $autoOpen = $isForm && ($errors->has('employee_ids') || $errors->has('employee_ids.*') || old('employee_ids'));

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

<div
    class="modal fade"
    id="{{ $id }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $id }}-title"
    aria-hidden="true"
    @if ($isForm)
        data-assignment-add-modal
        data-auto-open="{{ $autoOpen ? 'true' : 'false' }}"
    @endif
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0">
            @if ($isForm)
                <form action="{{ $action }}" method="POST" class="d-flex flex-column overflow-hidden" data-assignment-modal-form>
                    @csrf
            @endif

            <div class="modal-header bg-soft-success">
                <h5 class="modal-title" id="{{ $id }}-title">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.button.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-3">
                <div data-employee-picker
                     data-url="{{ $url }}"
                     data-add-label="{{ $labels['add'] }}"
                     data-remove-label="{{ $labels['remove'] }}"
                     data-empty-label="{{ $labels['empty'] }}"
                     data-error-label="{{ $labels['error'] }}">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-0 text-muted">
                                <i class="fas fa-search" aria-hidden="true"></i>
                            </span>
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

                @if ($isForm)
                    <div data-selected-assignment-inputs></div>
                    <div data-assignment-modal-errors>
                        <x-form.error name="employee_ids" />
                        <x-form.error name="employee_ids.*" />
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light px-4" data-dismiss="modal">
                    {{ __('common.button.cancel') }}
                </button>
                @if ($isForm)
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save"></i>
                        {{ __('common.button.save') }}
                    </button>
                @else
                    <button type="button" class="btn btn-success px-4" data-assignment-confirm>
                        {{ $confirmLabel }}
                    </button>
                @endif
            </div>

            @if ($isForm)
                </form>
            @endif
        </div>
    </div>
</div>

<script type="application/json" data-initial-employees="{{ $id }}">@json($initialEmployees)</script>
@once
    @push('scripts')
        <script src="{{ asset('js/shared/employee-picker.js') }}"></script>
    @endpush
@endonce

@if ($isForm)
    @once
        @push('scripts')
            <script src="{{ asset('js/shared/assignment/assignment-add-modal.js') }}"></script>
        @endpush
    @endonce
@endif
