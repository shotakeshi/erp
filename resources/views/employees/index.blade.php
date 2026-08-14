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
                    <div class="row">
                        <div class="col-lg-6">
                            <x-action-button
                                    icon="fas fa-plus"
                                    type="primary"
                                    :href="route('employees.create')">
                                {{ __('common.button.add') . ' ' .  __('site.employees.title') }}
                            </x-action-button>
                        </div>
                        <div class="col-lg-6">
                            <a href="{{ route('employees.trash') }}" class="btn btn-outline-gray float-right">
                                <i class="fas fa-trash-alt"></i> {{ __('common.button.list_of_trash') }}
                            </a>
                        </div>
                    </div>
                    @include('employees._filter', ['action' => route('employees.index')])
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
    @if (session('generated_password'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const password = @json(session('generated_password'));
                Swal.fire({
                    title: @json(__('common.messages.password_generated')),
                    html: `<div class="text-left">
                        <p class="mb-2"> {{ __('common.messages.password_sent_by_email') }}</p>
                        <div class="input-group">
                            <input
                                type="text"
                                id="generated-password"
                                class="form-control"
                                value="${password}" readonly >
                                    <button
                                        type="button"
                                        class="btn btn-outline-secondary"
                                        id="copy-password" >
                                        <i class="fas fa-copy"></i>
                                        {{ __('common.button.copy') }}
                    </button>
                </div>
            </div>`,
                    icon: 'success',
                    confirmButtonText: @json(__('common.button.close')),
                });
                const popup = Swal.getPopup();
                if (!popup) { return; }
                const copyButton = popup.querySelector('#copy-password');
                if (!copyButton) {
                    return;
                }
                copyButton.addEventListener('click', async function () {
                    try {
                        await navigator.clipboard.writeText(password);
                        this.innerHTML = `<i class="fas fa-check"></i>{{ __('common.button.copied') }}`;
                        setTimeout(() => {
                            this.innerHTML = `
                            <i class="fas fa-copy"></i>
                            {{ __('common.button.copy') }}
                            `;
                        }, 1500);
                    } catch (error) {
                        Swal.showValidationMessage(
                                @json(__('common.messages.copy_failed'))
                        );
                    }
                });
            });
        </script>
    @endif
@endpush