@push('title', __('site.employees.title'))
@push('css')
    <link href="{{ asset('css/dropify/dropify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet" />
    <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('plugins/timepicker/bootstrap-material-datetimepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('plugins/bootstrap-touchspin/css/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
@endpush
@extends('layouts.master')
@section('content')
    <x-page-title
            title="{{ __('site.employees.edit') }}"
            :breadcrumbs="[
            ['title' => __('site.employees.title'), 'url' => route('employees.index')],
            ['title' => __('site.employees.edit')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form class="form-horizontal form-material mb-0" action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        {{ __('site.employees.personal_information') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <x-form.label for="avatar">
                                    {{ __('site.employees.avatar') }}
                                </x-form.label>
                                <input type="file" name="avatar"
                                       accept="image/jpeg,image/png,image/webp"
                                       class="dropify"
                                       data-default-file="{{ image_url($employee->avatar,'images/avatar-upload.png') }}"/>
                                @if ($employee->avatar)
                                    <div class="form-check mt-2">
                                        <input
                                                type="checkbox"
                                                class="form-check-input"
                                                name="remove_avatar"
                                                id="remove_avatar"
                                                value="1"
                                        >
                                        <label class="form-check-label" for="remove_avatar">
                                            {{ __('site.employees.remove_avatar') }}
                                        </label>
                                    </div>

                                @endif
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <x-form.input
                                                name="employee_id"
                                                label="{{ __('site.employees.employee_id') }}"
                                                placeholder="{{ __('site.employees.employee_id') }}"
                                                :value="old( 'employee_id', $employee->employee_id )"
                                                required
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <x-form.datepicker
                                                name="dob"
                                                label="{{ __('site.employees.dob') }}"
                                                :value="old('dob',$employee->dob?->format('d/m/Y'))"
                                                required
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <x-form.select
                                                label="{{ __('site.employees.sex') }}"
                                                name="gender"
                                                :options="\App\Enums\Gender::options()"
                                                :selected="old( 'gender', $employee->gender?->value )"
                                                required
                                        />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <x-form.input
                                                name="first_name"
                                                label="{{ __('site.employees.firstname') }}"
                                                placeholder="{{ __('site.employees.firstname') }}"
                                                :value="old( 'first_name', $employee->first_name )"
                                                required
                                        />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-form.input
                                                name="last_name"
                                                label="{{ __('site.employees.lastname') }}"
                                                placeholder="{{ __('site.employees.lastname') }}"
                                                :value="old( 'first_name', $employee->last_name )"
                                                required
                                        />
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <x-form.input
                                                name="email"
                                                label="{{ __('site.employees.email') }}"
                                                placeholder="{{ __('site.employees.email') }}"
                                                :value="old( 'first_name', $employee->email )"
                                                required
                                        />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-form.input
                                                name="phone"
                                                label="{{ __('site.employees.phone') }}"
                                                placeholder="{{ __('site.employees.phone') }}"
                                                :value="old( 'first_name', $employee->phone )"
                                                required
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!--end card-body-->
                </div><!--end card-->
                <div class="card">
                    <div class="card-header">
                        {{ __('site.employees.address') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-12">
                                <x-form.input
                                        name="address"
                                        label="{{ __('site.employees.address') }}"
                                        placeholder="{{ __('site.employees.address') }}"
                                        :value="old( 'address', $employee->address )"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <x-form.select
                                        label="{{ __('site.employees.city') }}"
                                        name="city"
                                        :options="$provinces"
                                        option-value="code"
                                        option-label="name"
                                        :selected="old( 'city', $employee->city )"
                                        select2
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.select
                                        label="{{ __('site.employees.state') }}"
                                        name="state"
                                        :selected="old( 'state', $employee->state )"
                                        select2
                                />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        {{ __('site.employees.employment_details') }}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <x-form.select
                                        name="department_id"
                                        label="{{ __('site.departments.title') }}"
                                        placeholder="{{ __('site.positions.choose_department') }}"
                                        select2
                                        :options="$departments"
                                        :selected="old( 'department_id', $employee->department_id )"
                                        option-value="id"
                                        option-label="name"
                                />
                            </div>
                            <div class="col-lg-6">
                                <x-form.select
                                        name="position_id"
                                        label="{{ __('site.employees.position') }}"
                                        :options="[]"
                                        :selected="old( 'position_id', $employee->position_id )"
                                        option-value="id"
                                        option-label="name"
                                        select2
                                />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <x-form.select
                                        name="reporting_manager_id"
                                        label="{{ __('site.employees.reporting_manager') }}"
                                        select2
                                        :options="$employees"
                                        :selected="old( 'reporting_manager_id', $employee->reporting_manager_id )"
                                        option-value="id"
                                        option-label="employee_id_full_name"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.datepicker
                                        name="date_of_joining"
                                        label="{{ __('site.employees.date_of_joining') }}"
                                        required
                                        :value="old('date_of_joining',$employee->date_of_joining?->format('d/m/Y'))"
                                />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <x-form.select
                                        label="{{ __('site.employees.contract_type') }}"
                                        name="contract_type"
                                        :options="\App\Enums\ContractType::options()"
                                        :selected="old( 'contract_type', $employee->contract_type?->value )"
                                        required
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input
                                        name="salary"
                                        label="{{ __('site.employees.salary') }}"
                                        placeholder="{{ __('site.employees.salary') }}"
                                        :value="old( 'salary', $employee->salary )"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <x-form-actions :show-reset="false" show-cancel :url-cancel="route('employees.index')" />
                        </div>
                    </div>
                </div>
            </form>
        </div> <!--end col-->
    </div><!--end row-->
@endsection
@push('scripts')
    <script src="{{ asset('js/dropify/dropify.min.js') }}"></script>
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('plugins/timepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-touchspin/js/jquery.bootstrap-touchspin.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.forms-advanced.js') }}"></script>
    <script>
        $('.dropify').dropify();

        moment.updateLocale('en', {
            months: [
                '01', '02', '03', '04', '05', '06',
                '07', '08', '09', '10', '11', '12'
            ],
            monthsShort: [
                '01', '02', '03', '04', '05', '06',
                '07', '08', '09', '10', '11', '12'
            ]
        });

        $('#dob, #date-of-joining').on('show.daterangepicker', function () {
            $('.monthselect option').each(function (index) {
                $(this).text(String(index + 1).padStart(2, '0'));
            });
        });

        /*
        |--------------------------------------------------------------------------
        | City -> Ward
        |--------------------------------------------------------------------------
        */

        const $city = $('#city');
        const $state = $('#state');

        const statePlaceholder = @json(__('site.select'));

        function loadWards(
            cityCode,
            selectedWardCode = null
        ) {
            $state.empty();

            $state.append(
                new Option(
                    statePlaceholder,
                    ''
                )
            );

            if (!cityCode) {
                $state.trigger('change');
                return;
            }

            $state.html(
                '<option value="">Loading...</option>'
            );

            $.get(
                `/locations/wards/${cityCode}`,
                function (wards) {
                    $state.empty();
                    $state.append(
                        new Option(
                            statePlaceholder,
                            ''
                        )
                    );

                    wards.forEach(function (ward) {
                        const option = new Option(
                            ward.name_with_type,
                            ward.code,
                            false,
                            ward.code == selectedWardCode
                        );
                        $state.append(option);
                    });
                    $state.trigger('change');
                }
            );
        }


        $city.on('change', function () {
            loadWards(
                $(this).val()
            );
        });


        /*
        |--------------------------------------------------------------------------
        | Restore City / Ward
        |--------------------------------------------------------------------------
        */

        $(function () {
            const city = @json(
                old('city', $employee->city)
            );

            const state = @json(
                old('state', $employee->state)
            );

            if (!city) {
                return;
            }

            $city
                .val(city)
                .trigger('change.select2');

            loadWards(
                city,
                state
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Department -> Position
        |--------------------------------------------------------------------------
        */

        const departments = @json($departments);
        const $department = $('#department_id');
        const $position = $('#position_id');
        const positionPlaceholder = @json(
            __('site.employees.select_position')
        );

        function loadPositions(
            departmentId,
            selectedPositionId = null
        ) {

            $position.empty();
            $position.append(
                new Option(
                    positionPlaceholder,
                    ''
                )
            );

            if (!departmentId) {
                $position.trigger('change');
                return;
            }

            const department = departments.find(
                item => item.id == departmentId
            );

            if (!department) {
                $position.trigger('change');
                return;
            }

            department.positions.forEach(
                function (position) {

                    const option = new Option(
                        position.name,
                        position.id,
                        false,
                        position.id == selectedPositionId
                    );
                    $position.append(option);
                }
            );
            $position.trigger('change');
        }

        $department.on('change', function () {
            loadPositions(
                $(this).val()
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Restore Department / Position
        |--------------------------------------------------------------------------
        */

        $(function () {
            const departmentId = @json(
                old(
                    'department_id',
                    $employee->department_id
                )
            );

            const positionId = @json(
                old(
                    'position_id',
                    $employee->position_id
                )
            );

            if (!departmentId) {
                return;
            }

            $department
                .val(departmentId)
                .trigger('change.select2');

            loadPositions(
                departmentId,
                positionId
            );
        });

    </script>
@endpush