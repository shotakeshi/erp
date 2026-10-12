@php
    $initialEmployees = $selectedEmployees->map(static fn ($employee): array => [
        'employee_id' => $employee->id,
        'name' => $employee->full_name,
        'detail' => $employee->position?->name ?? '-',
        'avatar_url' => $employee->avatar ? image_url($employee->avatar) : null,
    ])->values();
@endphp

<div class="modal fade"
     id="project-add-assignment-modal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="project-add-assignment-title"
     aria-hidden="true"
     data-auto-open="{{ $errors->has('employee_ids') || $errors->has('employee_ids.*') || old('employee_ids') ? 'true' : 'false' }}"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <form action="{{ route('projects.assignments.store', $project) }}" method="POST" class="modal-content" data-project-add-assignment-form>
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="project-add-assignment-title">{{ __('site.projects.members_modal_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('common.button.close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <x-employee-picker
                    :url="route('projects.assignments.employee-options', $project)"
                    :labels="[
                        'remove' => __('site.projects.remove_member'),
                        'members' => __('site.projects.members'),
                    ]"
                />
                <div data-project-selected-inputs></div>
                <x-form.error name="employee_ids" />
                <x-form.error name="employee_ids.*" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">{{ __('common.button.cancel') }}</button>
                <button type="submit" class="btn btn-primary">{{ __('common.button.save') }}</button>
            </div>
        </form>
    </div>
</div>

<script type="application/json" data-project-selected-employees>@json($initialEmployees)</script>

@push('scripts')
    <script>
        $(function () {
            const $modal = $('#project-add-assignment-modal');
            const $form = $modal.find('[data-project-add-assignment-form]');
            const $inputs = $form.find('[data-project-selected-inputs]');
            const initialEmployees = JSON.parse($('[data-project-selected-employees]').text());
            const picker = EmployeePicker.create($modal.find('[data-employee-picker]'), function (employees) {
                $inputs.empty();
                employees.forEach(function (employee) {
                    $('<input>', { type: 'hidden', name: 'employee_ids[]', value: employee.employee_id }).appendTo($inputs);
                });
            });

            $modal.on('show.bs.modal', function () {
                picker.open(initialEmployees);
            });
            $modal.on('hidden.bs.modal', picker.close);

            if ($modal.attr('data-auto-open') === 'true') {
                $modal.modal('show');
            }
        });
    </script>
@endpush
