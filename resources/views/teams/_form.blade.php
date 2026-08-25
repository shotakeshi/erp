@php
    $team = $team ?? null;
@endphp

<form class="form-horizontal form-material mb-0" action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')  @method($method)  @endif
    <div class="card">
        <div class="card-header">
            {{ __('site.teams.detail') }}
        </div>
        <div class="card-body">
            <div class="form-group row">
                <div class="col-lg-8">
                    <x-form.input
                        name="name"
                        label="{{ __('site.teams.name') }}"
                        :value="$team?->name"
                        placeholder="{{ __('site.teams.name_placeholder') }}"
                        required
                        aria-required="true"
                        autofocus
                    />
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <x-form.input
                        name="code"
                        label="{{ __('site.teams.code') }}"
                        :value="$team?->code"
                        placeholder="{{ __('site.teams.code_placeholder') }}"
                        format="uppercase"
                        required
                        aria-required="true"
                    />
                </div>
            </div>

            <div class="form-group row mt-3">
                <div class="col-lg-12">
                    <x-form.textarea
                        name="description"
                        label="{{ __('site.teams.description') }}"
                        :value="$team?->description"
                        placeholder="{{ __('site.teams.description_placeholder') }}"
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
</form>
