@push('title', __('site.employees.title'))
@push('css')
    <link href="{{ asset('css/dropify/dropify.min.css') }}" rel="stylesheet">
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
            <div class="card">
                <div class="card-body">
                    <form class="form-horizontal form-material mb-0">
                        <div class="form-group row">
                            <div class="col-lg-3">
                                <input type="file" class="dropify" data-default-file="{{ asset('images/users/dr-pro.png') }}"/>
                            </div>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label for="employee_id">{{ __('site.employees.employee_id') }}</label>
                                        <input type="text" placeholder="{{ __('site.employees.employee_id') }}" class="form-control" name="employee_id" id="employee_id">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-6">
                                        <label for="first_name">{{ __('site.employees.firstname') }}</label>
                                        <input type="text" placeholder="{{ __('site.employees.firstname') }}" class="form-control" name="first_name" id="first_name">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="last_name">{{ __('site.employees.lastname') }}</label>
                                        <input type="text" placeholder="{{ __('site.employees.lastname') }}" class="form-control" name="last_name" id="last_name">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4 ">
                                        <input type="text" placeholder="Date of Birth" class="form-control" name="DOB" id="DOB">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" placeholder="Speciality" class="form-control" name="Speciality" id="Speciality">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" placeholder="Phone No" class="form-control" name="Phone_No" id="Phone_No">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <input type="email" placeholder="Email" class="form-control" name="Email" id="Email">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" placeholder="Web URL" class="form-control" name="Web_URL" id="Web_URL">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-6">
                                <input type="text" placeholder="Digree" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <select class="form-control">
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <x-form-actions show-reset show-cancel :url-cancel="route('employees.index')" />
                        </div>
                    </form>
                </div> <!--end card-body-->
            </div><!--end card-->
        </div> <!--end col-->
    </div><!--end row-->
@endsection
@push('scripts')
    <script src="{{ asset('js/dropify/dropify.min.js') }}"></script>
    <script>
        $('.dropify').dropify();
    </script>
@endpush