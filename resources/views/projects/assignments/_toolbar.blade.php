@php
    $resetUrl = route('projects.show', ['project' => $project, 'tab' => \App\Http\Controllers\ProjectController::MEMBERS_TAB]);
@endphp

<div class="card">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <form action="{{ route('projects.show', $project) }}" method="GET">
                    <input type="hidden" name="tab" value="{{ \App\Http\Controllers\ProjectController::MEMBERS_TAB }}">
                    <div class="row">
                        <div class="col-lg-8">
                            <x-form.input
                                name="search"
                                placeholder="{{ __('site.projects.member_search_placeholder') }}"
                                :value="request('search')"
                            />
                        </div>
                        <div class="col-lg-2 mt-2 mt-lg-0">
                            <button type="submit" class="btn btn-outline-gray w-100">
                                {{ __('common.button.search') }}
                            </button>
                        </div>
                        <div class="col-lg-2 mt-2 mt-lg-0">
                            <a href="{{ $resetUrl }}" class="btn btn-outline-danger w-100">
                                {{ __('common.button.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-lg-6 mt-2 mt-lg-0">
                <div class="d-flex align-items-center justify-content-lg-end">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#project-add-assignment-modal">
                        <i class="fas fa-plus mr-1" aria-hidden="true"></i>
                        {{ __('site.projects.add_member') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
