@push('title', __('site.employees.title'))
@extends('layouts.master')
@section('content')
    <x-page-title
        title="{{ __('site.profiles.change_password') }}"
        :breadcrumbs="[
        ['title' => __('site.profiles.change_password')],
    ]">
    </x-page-title>
    <div class="row">
        <div class="col-lg-6 mx-auto">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-gray">
                <i class="fas fa-arrow-left"></i> {{ __('site.profiles.back') }}
            </a>
            <form class="form-horizontal form-material mt-3 mb-0" action="{{ route('profiles.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-key"></i> {{ __('site.profiles.change_password') }}
                    </div>
                    <div class="card-body">
                        <span class="text-muted font-italic">{{ __('site.profiles.note_change_password') }}</span>
                        <div class="mt-3">
                            <x-form.input
                                    name="current_password"
                                    type="password"
                                    label="{{ __('site.profiles.current_password') }}"
                                    placeholder="{{ __('site.profiles.current_password') }}"
                                    required
                            />
                        </div>
                        <hr>
                        <div>
                            <x-form.input
                                    name="password"
                                    type="password"
                                    label="{{ __('site.profiles.password') }}"
                                    placeholder="{{ __('site.profiles.password') }}"
                                    required
                            />
                        </div>
                            <div class="mt-3">
                                <x-form.input
                                        name="password_confirmation"
                                        type="password"
                                        label="{{ __('site.profiles.password_confirmation') }}"
                                        placeholder="{{ __('site.profiles.password_confirmation') }}"
                                        required
                                />
                            </div>
                        <button type="submit" class="btn btn-primary waves-effect waves-light mt-4 float-right">
                            <i class="fas fa-save"></i> {{ __('site.profiles.update_password') }}
                        </button>
                    </div> <!--end card-body-->
                </div><!--end card-->
            </form>
        </div> <!--end col-->
    </div><!--end row-->
@endsection