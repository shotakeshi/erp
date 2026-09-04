@php
    $team = $team ?? null;
@endphp

@push('css')
    <link href="{{ asset('css/dropify/dropify.min.css') }}" rel="stylesheet">
@endpush

<form class="form-horizontal form-material mb-0" action="{{ $action }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')  @method($method)  @endif
    <div class="row">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-header">
                    {{ __('site.teams.detail') }}
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-4">
                            <x-form.label for="logo">
                                {{ __('site.teams.logo') }}
                            </x-form.label>
                            <input
                                    type="file"
                                    name="logo"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="dropify"
                                    data-default-file="{{ image_url($team?->logo) }}"
                            />
                            @if ($team?->logo)
                                <div class="form-check mt-2">
                                    <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="remove_logo"
                                            id="remove_logo"
                                            value="1"
                                    >
                                    <label class="form-check-label" for="remove_logo">
                                        {{ __('site.teams.remove_logo') }}
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div class="col-lg-8">
                            <div class="row">
                                <div class="col-lg-12">
                                    <x-form.input
                                            name="name"
                                            label="{{ __('site.teams.name') }}"
                                            :value="$team?->name"
                                            placeholder="{{ __('site.teams.name_placeholder') }}"
                                            required
                                            autofocus
                                    />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 mt-3">
                                    <x-form.input
                                            name="code"
                                            label="{{ __('site.teams.code') }}"
                                            :value="$team?->code"
                                            placeholder="{{ __('site.teams.code_placeholder') }}"
                                            format="uppercase"
                                            required
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row mt-3">
                        <div class="col-lg-12">
                            <x-form.textarea
                                    name="description"
                                    label="{{ __('site.teams.description') }}"
                                    :value="$team?->description"
                                    placeholder="{{ __('site.teams.description') }}"
                                    :rows="4"
                            />
                        </div>
                    </div>

                    <div class="form-group">
                        <x-form-actions
                                :show-reset="$method === 'POST'"
                                show-cancel
                                :url-cancel="route('teams.index')"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">
                    Add member to team
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table  table-bordered" id="makeEditable">
                            <thead>
                                <tr>
                                    <th>Firstname</th>
                                    <th>Detail</th>
                                    <th>Role </th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Phạm Quốc Thắng</td>
                                    <td>Developer - Senior</td>
                                    <td>
                                        <input type="text" class="form-control" name="role">
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-circle btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Default</td>
                                    <td>Defaultson</td>
                                    <td>
                                        <input type="text" class="form-control" name="role">
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-circle btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Default</td>
                                    <td>Defaultson</td>
                                    <td>
                                        <input type="text" class="form-control" name="role">
                                    </td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-circle btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <span class="float-right">
                        <button id="but_add" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-plus"></i> Add New Member
                        </button>
                    </span><!--end table-->
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
    <script src="{{ asset('js/dropify/dropify.min.js') }}"></script>
    <script>
        $('.dropify').dropify();
    </script>
@endpush
