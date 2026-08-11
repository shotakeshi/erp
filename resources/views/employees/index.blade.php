@push('title', __('site.employees.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
        title="{{ __('site.employees.title') }}"
        :breadcrumbs="[
            ['title' => __('site.employees.title'), 'url' => route('employees.index')],
            ['title' => __('site.employees.list')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <x-action-button
                            icon="fas fa-plus"
                            type="primary"
                            :href="route('employees.create')">
                        {{ __('common.button.add') . ' ' .  __('site.employees.title') }}
                    </x-action-button>
                    @include('employees._filter')
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