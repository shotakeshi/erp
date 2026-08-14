@push('title', __('site.departments.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
            title="{{ __('site.departments.show') }}"
            :breadcrumbs="[
            ['title' => __('site.departments.title'), 'url' => route('departments.index')],
            ['title' => __('site.departments.show')],
        ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <a href="{{ route('departments.index') }}" class="btn btn-sm btn-outline-gray">
                <i class="fas fa-arrow-left"></i> {{ __('site.departments.back_to_department') }}
            </a>
            <div class="float-right">
                <a href="{{ route('departments.edit', $department) }}" class="btn btn-sm btn-outline-warning">
                    <i class="fas fa-edit"></i> {{ __('site.departments.edit') }}
                </a>
                <x-form.delete-button
                    :action="route('departments.destroy',$department)"
                    title="{{ __('site.departments.delete') }}"
                />
            </div>
            <div class="card mt-3">
                <div class="card-header">
                    {{ __('site.departments.detail') }}
                </div>
                <div class="card-body pl-5 pr-5">
                    <h4>{{ $department->name }}</h4>
                    @if($department->parent?->name)
                        <div class="mb-2">
                            {{ __('site.departments.sub_department_of') }} <a class="text-primary" href="{{ route('departments.show', $department->parent->id) }}">{{ $department->parent->name }}</a>
                        </div>
                    @endif
                    <p>{{ $department->description ?? '-' }}</p>
                    <i class="fas fa-users"></i> {{ $department->employees()->count() }} {{ __('site.departments.employees') }}
                    @if($department->cost_center)
                        <span class="badge badge-light pt-1 pb-1 pl-3 pr-3 ml-3">
                            {{ $department->cost_center }}
                        </span>
                    @endif
                    @if($department->head)
                        <span class="text-muted font-italic ml-3">
                            Head: {{ $department->head->employee_id_full_name }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            {{ __('site.departments.sub-department') }}
                            <div class="float-right">{{ $department->children()->count() }}</div>
                        </div>
                        <div class="card-body">
                            @if($department->children()->count() > 0)
                                @foreach($department->children as $childen)
                                    <a class="text-primary" href="{{ route('departments.show', $childen->id) }}">{{ $childen->name }}</a>
                                    <div class="float-right text-muted">{{ $childen->employees()->count() }} {{ __('site.departments.staff') }}</div>
                                @endforeach
                            @else
                                <p class="text-muted">{{ __('site.departments.no_sub_department') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            {{ __('site.departments.position') }}
                            <div class="float-right">{{ $department->positions()->count() }}</div>
                        </div>
                        <div class="card-body">
                            @if($department->positions()->count() > 0)
                                @foreach($department->positions as $position)
                                    <div class="mb-2">
                                        <i class="fas fa-briefcase text-muted"></i>
                                        {{ $position->name }}
                                        <div class="float-right text-muted">{{ $position->employees()->count() }} {{ __('site.departments.staff') }}</div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">{{ __('site.departments.no_position') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--end col-->
    </div><!--end row-->
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