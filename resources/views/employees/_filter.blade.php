<form action="{{ route('employees.index') }}" method="GET" class="mt-3">
    <div class="row">
        <div class="col-lg-4">
            <x-form.input
                    name="search"
                    placeholder="{{ __('site.employees.search_placeholder') }}"
                    :value="request('search')"
            />
        </div>
        <div class="col-lg-1">
            <button type="submit" class="btn btn-outline-gray w-100">
                {{ __('common.button.search') }}
            </button>
        </div>
        <div class="col-lg-1">
            <a href="{{ route('employees.index') }}" class="btn btn-outline-danger w-100">
                {{ __('common.button.reset') }}
            </a>
        </div>
        <div class="col-lg-6">
            <button type="button" class="btn btn-outline-gray float-right" data-toggle="collapse" data-target="#filter" aria-expanded="false" aria-controls="filter">
                <i class="ti ti-filter"></i> {{ __('common.button.filter') }}
            </button>
        </div>
        <div class="col-lg-12">
            <div class="collapse" id="filter">
                <hr>
                <div class="row mt-3">
                    <div class="col-lg-2">
                        <x-form.select
                                name="status"
                                placeholder="{{ __('common.filters.all_status') }}"
                                :options="\App\Enums\UserStatus::options()"
                                option-value="value"
                                option-label="label"
                                :selected="request('status')"
                        />
                    </div>
                    <div class="col-lg-2">
                        <x-form.select
                                name="department_id"
                                placeholder="{{ __('common.filters.all_department') }}"
                                :options="$departments"
                                option-value="id"
                                option-label="name"
                                :selected="request('department_id')"
                        />
                    </div>
                    <div class="col-lg-2">
                        <x-form.select
                                name="position_id"
                                placeholder="{{ __('common.filters.all_position') }}"
                                :options="$positions"
                                option-value="id"
                                option-label="name"
                                :selected="request('position_id')"
                        />
                    </div>
                    <div class="col-lg-2">
                        <x-form.select
                                name="contract_type"
                                placeholder="{{ __('common.filters.all_contract_type') }}"
                                :options="\App\Enums\ContractType::options()"
                                option-value="value"
                                option-label="label"
                                :selected="request('contract_type')"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>