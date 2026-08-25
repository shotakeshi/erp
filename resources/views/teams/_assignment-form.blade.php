@php
    $isMemberAssignment = $assignmentType === 'member';
    $modalId = 'add-' . $assignmentType . '-modal';
    $oldEmployeeIds = collect(old('employee_ids', []));
    $shouldOpen = $errors->has('employee_ids')
        || $errors->get('employee_ids.*') !== []
        || $errors->has('start_date');
@endphp

<div
    class="modal fade team-add-assignment-modal"
    id="{{ $modalId }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $modalId }}-title"
    aria-hidden="true"
    data-auto-open="{{ $shouldOpen ? 'true' : 'false' }}"
>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ $storeAction }}" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}-title">
                        {{ $isMemberAssignment
                            ? __('site.teams.add_members_to_team', ['team' => $team->name])
                            : __('site.teams.add_managers_to_team', ['team' => $team->name]) }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.button.cancel') }}">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <x-form.label for="{{ $assignmentType }}-employee-ids" required>
                            {{ __('site.teams.employee_ids') }}
                        </x-form.label>
                        <select
                            id="{{ $assignmentType }}-employee-ids"
                            name="employee_ids[]"
                            class="form-control team-employee-select @error('employee_ids') is-invalid @enderror @error('employee_ids.*') is-invalid @enderror"
                            multiple
                            required
                            aria-required="true"
                            data-placeholder="{{ __('site.teams.select_employees') }}"
                        >
                            @foreach ($employees as $employee)
                                @php
                                    $isCurrentlyAssigned = $assignedEmployeeIds->contains($employee->id);
                                    $isSelected = $oldEmployeeIds->contains($employee->id);
                                @endphp
                                <option value="{{ $employee->id }}"
                                        @selected($isSelected)
                                        @disabled($isCurrentlyAssigned)
                                >
                                    {{ $employee->employee_id_full_name }}
                                    @if ($isCurrentlyAssigned)
                                        — {{ __('site.teams.already_assigned') }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <x-form.error name="employee_ids" />
                        <x-form.error name="employee_ids.*" />
                    </div>

                    <div class="form-group mb-0">
                        <x-form.input
                            name="start_date"
                            type="date"
                            label="{{ __('site.teams.start_date') }}"
                            :value="now()->toDateString()"
                            :max="now()->toDateString()"
                            required
                            aria-required="true"
                        />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-gray" data-dismiss="modal">
                        {{ __('common.button.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-plus mr-1"></i>
                        {{ $isMemberAssignment ? __('site.teams.add_members') : __('site.teams.add_managers') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('css')
    <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('js/team-assignment-form.js') }}"></script>
@endpush
