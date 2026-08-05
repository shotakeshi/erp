@push('title', __('site.positions.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
            title="{{ __('site.positions.add') }}"
            :breadcrumbs="[
            ['title' => __('site.positions.title'), 'url' => route('positions.index')],
            ['title' => __('site.positions.add')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <form class="form-horizontal form-material mb-0" action="{{ route('positions.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header">
                        {{ __('site.positions.detail') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <x-form.input
                                    name="name"
                                    label="{{ __('site.positions.name') }}"
                                    placeholder="{{ __('site.positions.name_eg') }}"
                                    required
                                />
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <div class="col-lg-6">
                                <x-form.select
                                        name="department_id"
                                        label="{{ __('site.positions.department') }}"
                                        placeholder="{{ __('site.positions.choose_department') }}"
                                        select2
                                        :options="$departments"
                                        option-value="id"
                                        option-label="name"
                                        required
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input
                                        name="level"
                                        label="{{ __('site.positions.level_grade') }}"
                                        placeholder="{{ __('site.positions.eg_level_grade') }}"
                                />
                            </div>
                        </div>
                        <div class="form-group row mt-3">
                            <div class="col-md-6">
                                <x-form.input
                                        name="salary_min"
                                        label="{{ __('site.positions.salary_min') }}"
                                        placeholder="{{ __('site.positions.salary_min') }}"
                                        :value="0"
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input
                                        name="salary_max"
                                        label="{{ __('site.positions.salary_max') }}"
                                        placeholder="{{ __('site.positions.salary_max') }}"
                                        :value="0"
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
                            <x-form-actions show-reset show-cancel :url-cancel="route('positions.index')" />
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