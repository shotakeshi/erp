@push('title', __('site.departments.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
        title="{{ __('site.departments.title') }}"
        :breadcrumbs="[
            ['title' => __('site.departments.title'), 'url' => route('departments.index')],
            ['title' => __('site.departments.list')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <x-action-button
                            icon="fas fa-plus"
                            type="primary"
                            :href="route('departments.create')">
                        {{ __('common.button.add') . ' ' .  __('site.departments.title') }}
                    </x-action-button>
                    @include('departments._table', ['departments', $departments])
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!-- end col -->
    </div>
@endsection