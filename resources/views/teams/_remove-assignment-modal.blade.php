@php
    $isMemberAssignment = $assignmentType === 'member';
    $modalId = 'remove-' . $assignmentType . '-assignment-modal';
    $reopenAssignment = $assignments->first(
        static fn ($assignment): bool => (string) $assignment->employee_id === (string) old('remove_employee_id'),
    );
    $shouldOpen = ($errors->has('end_date') || $errors->has('end_reason_note'))
        && $reopenAssignment !== null;
    $reopenEmployee = $reopenAssignment?->employee;
@endphp

<div
    class="modal fade team-remove-assignment-modal"
    id="{{ $modalId }}"
    data-auto-open="{{ $shouldOpen ? 'true' : 'false' }}"
    @if ($reopenAssignment && $reopenEmployee)
        data-assignment-action="{{ route($destroyRoute, [$team, $reopenEmployee]) }}"
        data-employee-id="{{ $reopenEmployee->id }}"
        data-employee-name="{{ $reopenEmployee->full_name }}"
        data-start-date="{{ $reopenAssignment->start_date->toDateString() }}"
        data-assignment-description="{{ __(
            $isMemberAssignment
                ? 'site.teams.remove_member_confirmation'
                : 'site.teams.remove_manager_confirmation',
            ['employee' => $reopenEmployee->full_name],
        ) }}"
    @endif
>
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" data-remove-assignment-form>
                @csrf
                @method('DELETE')

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}-title">
                        {{ $isMemberAssignment ? __('site.teams.remove_member_title') : __('site.teams.remove_manager_title') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <p class="mb-3" data-remove-assignment-description>
                        @if ($reopenEmployee)
                            {{ __(
                                $isMemberAssignment
                                    ? 'site.teams.remove_member_confirmation'
                                    : 'site.teams.remove_manager_confirmation',
                                ['employee' => $reopenEmployee->full_name],
                            ) }}
                        @endif
                    </p>

                    <input type="hidden" name="remove_employee_id" value="{{ old('remove_employee_id') }}" data-remove-employee-id>

                    <div class="form-group mb-0">
                        <x-form.datepicker
                            name="end_date"
                            label="{{ __('site.teams.end_date') }}"
                            :value="old('end_date', now()->format('d/m/Y'))"
                            :min-date="$reopenAssignment?->start_date?->format('d/m/Y')"
                            :max-date="now()->format('d/m/Y')"
                            required
                            data-remove-assignment-end-date
                        />
                    </div>

                    <div class="form-group mt-3 mb-0">
                        <x-form.textarea
                            name="end_reason_note"
                            label="{{ __('site.teams.end_reason_note') }}"
                            placeholder="{{ __('site.teams.end_reason_note_placeholder') }}"
                            :rows="4"
                            maxlength="5000"
                            data-remove-assignment-end-reason-note
                        />
                    </div>

                    <small class="form-text text-muted mt-3">
                        {{ $isMemberAssignment
                            ? __('site.teams.membership_history_retained')
                            : __('site.teams.manager_history_retained') }}
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-gray" data-dismiss="modal">
                        {{ __('common.button.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-outline-danger" data-remove-assignment-submit>
                        <i class="fas fa-user-minus mr-1"></i>
                        {{ __('site.teams.remove') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
