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
            title="{{ __('site.employees.add') }}"
            :breadcrumbs="[
            ['title' => __('site.employees.title'), 'url' => route('employees.index')],
            ['title' => __('site.employees.add')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form class="form-horizontal form-material mb-0" action="{{ route('employees.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        {{ __('site.employees.personal_infomation') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <x-form.label for="avatar">
                                    {{ __('site.employees.avatar') }}
                                </x-form.label>
                                <input type="file" class="dropify" data-default-file="{{ asset('images/avatar-upload.png') }}"/>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <x-form.input
                                            name="employee_id"
                                            label="{{ __('site.employees.employee_id') }}"
                                            placeholder="{{ __('site.employees.employee_id') }}"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <x-form.datepicker
                                                name="dob"
                                                label="{{ __('site.employees.dob') }}"
                                                required
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <x-form.select
                                                label="{{ __('site.employees.sex') }}"
                                                name="gender"
                                                :options="\App\Enums\Gender::options()"
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
                                                required
                                        />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-form.input
                                                name="last_name"
                                                label="{{ __('site.employees.lastname') }}"
                                                placeholder="{{ __('site.employees.lastname') }}"
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
                                                required
                                        />
                                    </div>
                                    <div class="col-lg-6">
                                        <x-form.input
                                                name="phone"
                                                label="{{ __('site.employees.phone') }}"
                                                placeholder="{{ __('site.employees.phone') }}"
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
                            <div class="col-md-8">
                                <x-form.input
                                        name="address"
                                        label="{{ __('site.employees.address') }}"
                                        placeholder="{{ __('site.employees.address') }}"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input
                                        name="portal_code"
                                        label="{{ __('site.employees.portal_code') }}"
                                        placeholder="{{ __('site.employees.portal_code') }}"
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
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.select
                                        label="{{ __('site.employees.state') }}"
                                        name="state"
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
                                <label for="department-id">{{ __('site.employees.deparment') }}</label>
                                <select name="department_id" id="department-id" class="form-control">
                                    @foreach (\App\Enums\Gender::cases() as $gender)
                                        <option value="{{ $gender->value }}"
                                                @selected(old('gender', $employee->gender ?? 'Male') === $gender->value)>
                                            {{ $gender->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="position-id">{{ __('site.employees.position') }}*</label>
                                <select name="position_id" id="position-id" class="form-control">
                                    @foreach (\App\Enums\Gender::cases() as $gender)
                                        <option value="{{ $gender->value }}"
                                                @selected(old('gender', $employee->gender ?? 'Male') === $gender->value)>
                                            {{ $gender->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="reporting-manager-id">{{ __('site.employees.reporting_manager') }}</label>
                                <select name="reporting_manager_id" id="reporting-manager-id" class="form-control">
                                    @foreach (\App\Enums\Gender::cases() as $gender)
                                        <option value="{{ $gender->value }}"
                                                @selected(old('gender', $employee->gender ?? 'Male') === $gender->value)>
                                            {{ $gender->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <x-form.datepicker
                                        name="date_of_joining"
                                        label="{{ __('site.employees.date_of_joining') }}"
                                        required
                                />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <label for="contract-type">{{ __('site.employees.contract_type') }}</label>
                                <select name="contract_type" id="contract-type" class="form-control">
                                    @foreach (\App\Enums\Gender::cases() as $gender)
                                        <option value="{{ $gender->value }}"
                                                @selected(old('gender', $employee->gender ?? 'Male') === $gender->value)>
                                            {{ $gender->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="salary">{{ __('site.employees.salary') }}</label>
                                <input type="text"
                                       id="salary"
                                       class="form-control"
                                       name="salary"
                                       value="{{ old('salary') }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <x-form-actions show-reset show-cancel :url-cancel="route('employees.index')" />
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

        $('#city').on('change', function () {
            const province = $(this).val();
            $('#state').html('<option>Loading...</option>');
            $.get(`/locations/wards/${province}`, function (wards) {
                let html = '<option value="">-- {{ __('site.select') }} --</option>';
                wards.forEach(function (ward) {
                    html += `
                <option value="${ward.code}">
                    ${ward.name_with_type}
                </option>
            `;
                });
                $('#state').html(html);
            });
        });
    </script>
@endpush