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
            <form class="form-horizontal form-material mb-0">
                <div class="card">
                    <div class="card-header">
                        {{ __('site.employees.personal_infomation') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <label>{{ __('site.employees.avatar') }}</label>
                                <input type="file" class="dropify" data-default-file="{{ asset('images/avatar-upload.png') }}"/>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="employee-id">{{ __('site.employees.employee_id') }} *</label>
                                        <input type="text"
                                               placeholder="{{ __('site.employees.employee_id') }}"
                                               class="form-control"
                                               name="employee_id"
                                               id="employee-id"
                                               value="{{ old('employee_id') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="dob">{{ __('site.employees.dob') }}</label>
                                        <input type="text"
                                               id="dob"
                                               class="form-control"
                                               name="dob"
                                               value="{{ value('dob') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="gender">{{ __('site.employees.sex') }}</label>
                                        <select name="gender" id="gender" class="form-control">
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
                                    <div class="col-lg-6">
                                        <label for="first-name">{{ __('site.employees.firstname') }}*</label>
                                        <input type="text"
                                               placeholder="{{ __('site.employees.firstname') }}"
                                               class="form-control"
                                               name="first_name"
                                               id="first-name"
                                               value="{{ old('first_name') }}">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="last-name">{{ __('site.employees.lastname') }}*</label>
                                        <input type="text"
                                               placeholder="{{ __('site.employees.lastname') }}"
                                               class="form-control"
                                               name="last_name"
                                               id="last-name"
                                               value="{{ old('last_name') }}">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <label for="email">{{ __('site.employees.email') }}*</label>
                                        <input type="text"
                                               placeholder="{{ __('site.employees.email') }}"
                                               class="form-control"
                                               name="email"
                                               id="email"
                                               value="{{ old('first_name') }}">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="phone">{{ __('site.employees.phone') }}*</label>
                                        <input type="text"
                                               placeholder="{{ __('site.employees.phone') }}"
                                               class="form-control"
                                               name="phone"
                                               id="phone"
                                               value="{{ old('phone') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
{{--                        <div class="form-group row">--}}
{{--                            --}}{{--                                    'department_id', 'position_id', 'reporting_manager_id', '',--}}
{{--                            --}}{{--                                    'contract_type', 'salary',--}}

{{--
{{--                        </div>--}}

                    </div> <!--end card-body-->
                </div><!--end card-->
                <div class="card">
                    <div class="card-header">
                        {{ __('site.employees.address') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-8">
                                <label for="address">{{ __('site.employees.address') }}</label>
                                <input type="text"
                                       placeholder="{{ __('site.employees.address') }}"
                                       class="form-control"
                                       name="address"
                                       id="address">
                            </div>
                            <div class="col-md-4">
                                <label for="portal-code">{{ __('site.employees.portal_code') }}</label>
                                <input type="text"
                                       placeholder="{{ __('site.employees.portal_code') }}"
                                       class="form-control"
                                       name="portal_code"
                                       id="portal-code"
                                       value="{{ old('portal_code') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="province">{{ __('site.employees.city') }}</label>
                                <select id="province" name="city" class="form-select form-control">
                                    <option value="">{{ __('site.employees.city') }}</option>
                                    @foreach($provinces as $province)
                                        <option value="{{ $province['code'] }}">
                                            {{ $province['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="ward">{{ __('site.employees.state') }}</label>
                                <select id="ward" name="state"  class="select2 form-control mb-3 custom-select" style="width: 100%; height:36px;">
                                    <option value="">{{ __('site.employees.state') }}</option>
                                </select>
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
                                <label for="phone">{{ __('site.employees.position') }}*</label>
                                <select name="position_id" id="gender" class="form-control">
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
                            <div class="col-md-4">
                                <label for="date-of-joining">{{ __('site.employees.date_of_joining') }}</label>
                                <input type="text"
                                       id="date-of-joining"
                                       class="form-control"
                                       name="date_of_joining"
                                       value="{{ old('date_of_joining') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="date-of-joining">{{ __('site.employees.date_of_joining') }}</label>
                                <select name="reporting_manager_id" id="gender" class="form-control">
                                    @foreach (\App\Enums\Gender::cases() as $gender)
                                        <option value="{{ $gender->value }}"
                                                @selected(old('gender', $employee->gender ?? 'Male') === $gender->value)>
                                            {{ $gender->value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <x-form-actions show-reset show-cancel :url-cancel="route('employees.index')" />
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

        $('#dob, #date-of-joining').daterangepicker({
            singleDatePicker: true,
            showDropdowns: true,
            startDate: moment(),
            locale: {
                format: 'DD/MM/YYYY'
            }
        });

        $('#dob, #date-of-joining').on('show.daterangepicker', function () {
            $('.monthselect option').each(function (index) {
                $(this).text(String(index + 1).padStart(2, '0'));
            });
        });

        $('#province').on('change', function () {

            const province = $(this).val();

            $('#ward').html('<option>Loading...</option>');

            $.get(`/locations/wards/${province}`, function (wards) {

                let html = '<option value="">{{ __('site.employees.state') }}</option>';

                wards.forEach(function (ward) {
                    html += `
                <option value="${ward.code}">
                    ${ward.name_with_type}
                </option>
            `;
                });

                $('#ward').html(html);
            });
        });
    </script>
@endpush