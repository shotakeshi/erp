@push('title', __('site.departments.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
            title="{{ __('site.departments.add') }}"
            :breadcrumbs="[
            ['title' => __('site.departments.title'), 'url' => route('departments.index')],
            ['title' => __('site.departments.add')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form class="form-horizontal form-material mb-0" action="{{ route('departments.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        {{ __('site.departments.detail') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-6">
                                <x-form.input
                                    name="name"
                                    label="{{ __('site.departments.name') }}"
                                    placeholder="{{ __('site.departments.name_eg') }}"
                                    required
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input
                                        name="cost_center"
                                        label="{{ __('site.departments.cost_center') }}"
                                        placeholder="{{ __('site.departments.cost_center_eg') }}"
                                />
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <div class="col-lg-6">
                                <x-form.select
                                        name="parent_id"
                                        label="{{ __('site.departments.parent_department') }}"
                                        placeholder="{{ __('site.departments.no_parent_department') }}"
                                        select2
                                        :options="$departments"
                                        option-value="id"
                                        option-label="name"
                                />
                            </div>
                            <div class="col-lg-6">
                                <x-form.select
                                        name="head_id"
                                        label="{{ __('site.departments.department_head') }}"
                                        placeholder="{{ __('site.departments.no_department_head') }}"
                                        select2
                                        :options="$employees"
                                        option-value="id"
                                        option-label="employee_id_full_name"
                                />
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-12">
                                <x-form.textarea
                                        name="description"
                                        label="{{ __('site.departments.description') }}"
                                        placeholder="{{ __('site.departments.description') }}"
                                        rows="4"
                                />
                            </div>
                        </div>
                        <div class="form-group">
                            <x-form-actions show-reset show-cancel :url-cancel="route('departments.index')" />
                        </div>
                    </div> <!--end card-body-->
                </div><!--end card-->
            </form>
        </div> <!--end col-->
    </div><!--end row-->
@endsection
@push('css')
    <link href="{{ asset('plugins/select2/select2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush
@push('scripts')
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('plugins/select2/select2.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.forms-advanced.js') }}"></script>
@endpush