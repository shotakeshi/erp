@php
    $team = $team ?? null;
    $initialMembers = $initialMembers ?? [];
    $roles = $roles ?? collect();
    $memberInputs = old('members', []);
@endphp

@push('css')
    <link href="{{ asset('css/dropify/dropify.min.css') }}" rel="stylesheet">
@endpush

<form class="form-horizontal form-material mb-0"
      action="{{ $action }}"
      method="POST"
      enctype="multipart/form-data"
>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-lg-12">
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
                                id="logo"
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
                            <x-form.input
                                name="name"
                                label="{{ __('site.teams.name') }}"
                                :value="$team?->name"
                                placeholder="{{ __('site.teams.name') }}"
                                required
                                autofocus
                            />
                            <div class="mt-3">
                                <x-form.input
                                    name="code"
                                    label="{{ __('site.teams.code') }}"
                                    :value="$team?->code"
                                    placeholder="{{ __('site.teams.code') }}"
                                    format="uppercase"
                                    required
                                />
                            </div>
                            <div class="mt-3">
                                <x-form.textarea
                                    name="description"
                                    label="{{ __('site.teams.description') }}"
                                    :value="$team?->description"
                                    placeholder="{{ __('site.teams.description') }}"
                                    :rows="2"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('teams.partials._members-section', [
        'initialMembers' => $initialMembers,
        'memberInputs' => $memberInputs,
        'roles' => $roles,
    ])

    <div class="card">
        <div class="card-body">
            <x-form-actions
                show-reset
                show-cancel
                :url-cancel="route('teams.index')"
            />
        </div>
    </div>
</form>

@push('scripts')
    <script src="{{ asset('js/dropify/dropify.min.js') }}"></script>
    <script>
        $(function () {
            $('#logo').dropify({
                imgFileExtensions: ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'],
            });
        });
    </script>
@endpush
