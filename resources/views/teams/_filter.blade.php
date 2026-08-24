<form action="{{ $action ?? route('teams.index') }}" method="GET" class="mt-3">
    <div class="row">
        <div class="col-lg-4">
            <x-form.input
                name="search"
                :value="request('search')"
                placeholder="{{ __('site.teams.search_placeholder') }}"
                aria-label="{{ __('common.button.search') }}"
            />
        </div>
        <div class="col-lg-8">
            <button type="submit" class="btn btn-outline-gray mr-3">
                {{ __('common.button.search') }}
            </button>
            <a href="{{ $action ?? route('teams.index') }}" class="btn btn-outline-danger">
                {{ __('common.button.reset') }}
            </a>
        </div>
    </div>
</form>
