@push('title', __('site.employees.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
            title="{{ __('site.employees.title') }}"
            :breadcrumbs="[
            ['title' => __('site.employees.title'), 'url' => route('employees.index')],
            ['title' => __('site.employees.trash')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-gray">
                        <i class="fas fa-arrow-left"></i> {{ __('site.employees.list') }}
                    </a>
                    @include('employees._filter', ['action' => route('employees.trash')])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('employees._table', ['employees', $employees])
                    <x-pagination :paginator="$employees" />
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
@endpush