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
                    <x-action-button
                            icon="fas fa-plus"
                            type="primary"
                            :href="route('positions.create')">
                        {{ __('common.button.add') . ' ' .  __('site.positions.title') }}
                    </x-action-button>
                    @include('positions._table', ['positions', $positions])
                </div><!--end card-body-->
            </div><!--end card-->
        </div> <!-- end col -->
    </div>
@endsection