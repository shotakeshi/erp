@push('title', __('site.positions.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
        title="{{ __('site.positions.title') }}"
        :breadcrumbs="[
            ['title' => __('site.positions.title'), 'url' => route('positions.index')],
            ['title' => __('site.positions.list')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <x-action-button
                                    icon="fas fa-plus"
                                    type="primary"
                                    :href="route('positions.create')">
                                {{ __('common.button.add') . ' ' .  __('site.positions.title') }}
                            </x-action-button>
                        </div>
                        <div class="col-lg-3"></div>
                        <div class="col-lg-3">
                            <x-form.select
                                    name="department_id"
                                    placeholder="{{ __('site.positions.choose_department') }}"
                                    select2
                                    :options="$departments"
                                    option-value="id"
                                    option-label="name"
                                    onchange="filterDepartment(this.value)"
                                    :selected="request('department_id')"
                            />
                        </div>
                    </div>
                    @include('positions._table', ['positions', $positions])
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!-- end col -->
    </div>
@endsection
@push('css')
    <!-- Sweet Alert -->
    <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('plugins/animate/animate.css') }}" rel="stylesheet" type="text/css">
@endpush
@push('scripts')
    <!-- Sweet-Alert  -->
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('pages/jquery.sweet-alert.init.js') }}"></script>
    <script>
        function filterDepartment(departmentId) {
            const url = new URL(window.location.href);

            if (departmentId) {
                url.searchParams.set('department_id', departmentId);
            } else {
                url.searchParams.delete('department_id');
            }

            window.location.href = url.toString();
        }
    </script>
@endpush