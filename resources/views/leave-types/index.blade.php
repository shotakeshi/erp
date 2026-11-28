@push('title', __('site.leave_types.title'))
@extends('layouts.master')

@php
    $isEditing = $leaveType->exists;
    $switches = [
        'is_active',
        'is_paid',
        'allow_half_day',
        'deduct_from_balance',
        'is_carry_forward',
    ];
@endphp

@section('content')
    <x-page-title
        :title="__('site.leave_types.title')"
        :breadcrumbs="[
            ['title' => __('site.leave_types.title'), 'url' => route('leave-types.index')],
            ['title' => __('site.leave_types.list')],
        ]"
    />

    <div class="row">
        <div class="col-lg-4">
            <form class="form-horizontal form-material mb-0"
                action="{{ $isEditing ? route('leave-types.update', $leaveType) : route('leave-types.store') }}"
                method="POST"
            >
                @csrf
                @if($isEditing)
                    @method('PUT')
                @endif
                <div class="card">
                    <div class="card-header">
                        {{ $isEditing ? __('site.leave_types.edit') : __('site.leave_types.add') }}
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <x-form.input
                                name="name"
                                :value="$leaveType->name"
                                :label="__('site.leave_types.name')"
                                :placeholder="__('site.leave_types.name_eg')"
                                required
                            />
                        </div>
                        <div class="form-group">
                            <x-form.input
                                name="code"
                                :value="$leaveType->code"
                                :label="__('site.leave_types.code')"
                                :placeholder="__('site.leave_types.code_eg')"
                                maxlength="50"
                                required
                            />
                        </div>
                        <div class="form-group">
                            <x-form.textarea
                                name="description"
                                :value="$leaveType->description"
                                :label="__('site.leave_types.description')"
                                :placeholder="__('site.leave_types.description_eg')"
                                rows="3"
                            />
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <x-form.input
                                    name="max_days_per_year"
                                    type="number"
                                    :value="$leaveType->max_days_per_year"
                                    :label="__('site.leave_types.max_days_per_year')"
                                    :placeholder="__('site.leave_types.unlimited')"
                                    :help="__('site.leave_types.max_days_per_year_help')"
                                    min="0"
                                    step="1"
                                />
                            </div>
                            <div class="col-sm-6">
                                <x-form.input
                                    name="max_carry_forward"
                                    type="number"
                                    :value="$leaveType->max_carry_forward"
                                    :label="__('site.leave_types.max_carry_forward')"
                                    min="0"
                                    step="1"
                                />
                            </div>
                        </div>
                        <ul class="list-group">
                            @foreach($switches as $name)
                                <li class="list-group-item">
                                    <div class="custom-control custom-switch switch-success">
                                        <input type="hidden" name="{{ $name }}" value="0">
                                        <input
                                            id="{{ $name }}"
                                            name="{{ $name }}"
                                            type="checkbox"
                                            class="custom-control-input"
                                            value="1"
                                            @checked(old($name, $leaveType->{$name}))
                                        >
                                        <label class="custom-control-label" for="{{ $name }}">
                                            {{ __("site.leave_types.{$name}") }}
                                            <small class="d-block text-muted">{{ __("site.leave_types.{$name}_help") }}</small>
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="form-group mb-0">
                            <x-form-actions />
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table mb-0 table-centered table-hover">
                            <thead class="bg-light">
                                <tr>
                                    <th class="w-50">{{ __('site.leave_types.title') }}</th>
                                    <th class="text-nowrap">{{ __('site.leave_types.days_per_year') }}</th>
                                    <th class="text-nowrap">{{ __('site.leave_types.payment_type') }}</th>
                                    <th>{{ __('site.leave_types.status') }}</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($leaveTypes as $leaveType)
                                    <tr>
                                        <td>
                                            <div class="media">
                                                <span class="avatar-box thumb-md align-self-center mr-3">
                                                    <span class="avatar-title rounded bg-soft-primary">
                                                        <i class="fas fa-calendar-alt" aria-hidden="true"></i>
                                                    </span>
                                                </span>
                                                <div class="media-body align-self-center">
                                                    <h6 class="mt-0 mb-1">
                                                        {{ $leaveType->name }}
                                                        <span class="badge badge-light ml-1">{{ $leaveType->code }}</span>
                                                    </h6>
                                                    <p class="text-muted font-12 mb-1">{{ $leaveType->description ?? '-' }}</p>
                                                    @if($leaveType->allow_half_day)
                                                        <span class="badge badge-soft-info">{{ __('site.leave_types.half_day') }}</span>
                                                    @endif
                                                    @if($leaveType->deduct_from_balance)
                                                        <span class="badge badge-soft-warning">{{ __('site.leave_types.deduct') }}</span>
                                                    @endif
                                                    @if($leaveType->is_carry_forward)
                                                        <span class="badge badge-soft-purple">
                                                            {{ __('site.leave_types.carry_forward_max', ['days' => (float) $leaveType->max_carry_forward]) }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-nowrap">
                                            @if($leaveType->max_days_per_year === null)
                                                <span class="text-muted">{{ __('site.leave_types.unlimited') }}</span>
                                            @else
                                                <span class="font-weight-semibold">{{ (float) $leaveType->max_days_per_year }}</span>
                                                {{ __('site.leave_types.days') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($leaveType->is_paid)
                                                <span class="badge badge-soft-success">{{ __('site.leave_types.paid') }}</span>
                                            @else
                                                <span class="badge badge-soft-danger">{{ __('site.leave_types.unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($leaveType->is_active)
                                                <span class="badge badge-pill badge-success pl-2 pr-2">
                                                    <i class="ti ti-unlock"></i>
                                                    {{ __('site.leave_types.active') }}
                                                </span>
                                            @else
                                                <span class="badge badge-pill badge-danger pl-2 pr-2">
                                                    <i class="ti ti-close"></i>
                                                    {{ __('site.leave_types.inactive') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center text-nowrap">
                                            <a style="width: 34px; height: 34px"
                                               href="{{ route('leave-types.edit', $leaveType) }}"
                                               class="btn btn-sm btn-outline-warning"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <x-form.delete-button
                                                :action="route('leave-types.destroy', $leaveType)"
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">{{ __('site.leave_types.empty') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ asset('plugins/sweet-alert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('scripts')
    <script src="{{ asset('plugins/sweet-alert2/sweetalert2.min.js') }}"></script>
@endpush
