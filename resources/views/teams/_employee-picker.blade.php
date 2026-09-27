<div data-employee-picker
    data-url="{{ $employeeOptionsUrl }}"
    data-add-label="{{ __('common.button.add') }}"
    data-remove-label="{{ __('site.teams.remove') }}"
    data-empty-label="{{ __('site.teams.no_available_employees') }}"
    data-error-label="{{ __('site.teams.employee_search_failed') }}">
    <div class="input-group mb-3">
        <div class="input-group-prepend">
            <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-search" aria-hidden="true"></i></span>
        </div>
        <input type="search" class="form-control bg-light border-0"
            placeholder="{{ __('site.teams.search_employee_placeholder') }}"
            aria-label="{{ __('site.teams.search_employee_placeholder') }}" data-picker-search>
    </div>
    <div class="d-flex align-items-center mb-3">
        <h6 class="mb-0 mr-3">{{ __('site.teams.members') }}:</h6>
        <div class="d-flex align-items-center flex-wrap" data-picker-selected></div>
    </div>
    <p class="text-muted d-none" role="status" data-picker-loading>{{ __('site.teams.loading_employees') }}</p>
    <div class="alert alert-danger d-none" role="alert" data-picker-error>
        <span data-picker-error-message></span>
        <button type="button" class="btn btn-sm btn-outline-danger ml-2" data-picker-retry>{{ __('site.teams.retry') }}</button>
    </div>
    <div class="overflow-auto" style="max-height: 360px;" data-picker-scroll tabindex="0"
        aria-label="{{ __('site.teams.members') }}">
        <div data-picker-list></div>
    </div>
</div>

@push('scripts')
    <script>
        // Shared picker cho modal
        (function () {
            const normalize = (value) => value.trim().toLocaleLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd');

            // Render avatar ảnh hoặc avatar chữ cái đầu khi employee chưa có ảnh.
            const appendAvatar = function ($container, employee, avatarClass, fallbackClass) {
                if (employee.avatar_url) {
                    $('<img>', {
                        src: employee.avatar_url,
                        alt: employee.name,
                        title: employee.name,
                        class: `rounded-circle ${avatarClass}`,
                    }).appendTo($container);
                    return;
                }

                const initials = employee.name.split(' ').filter(Boolean).slice(0, 2)
                    .map((part) => part.charAt(0).toUpperCase()).join('');
                $('<span>', { class: `avatar-box ${avatarClass}`, title: employee.name })
                    .append($('<span>', { class: fallbackClass, text: initials })).appendTo($container);
            };

            const create = function ($root, onChange = function () {}) {
                const $search = $root.find('[data-picker-search]');
                const $scroll = $root.find('[data-picker-scroll]');
                const $list = $root.find('[data-picker-list]');
                const $selected = $root.find('[data-picker-selected]');
                const $loading = $root.find('[data-picker-loading]');
                const $error = $root.find('[data-picker-error]');
                // selection giữ employee người dùng đã chọn
                let selection = new Map();
                // employees là dữ liệu các employee đã tải cho search hiện tại.
                let employees = new Map();
                let excludedIds = [];
                let page = 0;
                let hasMore = false;
                let request = null;
                // Tăng revision khi hủy request để response cũ không render đè kết quả mới.
                let revision = 0;
                let timer;
                let search = '';

                const updateButton = function ($button, id) {
                    const selected = selection.has(id);
                    $button.text($root.data(selected ? 'remove-label' : 'add-label'))
                        .toggleClass('btn-light', !selected).toggleClass('btn-outline-danger', selected)
                        .attr('aria-pressed', String(selected));
                };

                const renderSelection = function () {
                    $selected.empty();
                    selection.forEach((employee) => appendAvatar($selected, employee, 'thumb-xs mr-1 mb-1',
                        'avatar-title bg-primary rounded-circle font-12 text-white'));
                    onChange(Array.from(selection.values()));
                };

                const cancelRequest = function () {
                    clearTimeout(timer);
                    revision += 1;
                    if (request) {
                        request.abort();
                        request = null;
                    }
                    $loading.addClass('d-none');
                    $root.attr('aria-busy', 'false');
                };

                const renderEmployee = function (employee, id) {
                    const $row = $('<div>', { class: 'd-flex align-items-center justify-content-between mb-3' });
                    const $person = $('<div>', { class: 'media align-items-center mr-2' });
                    appendAvatar($person, employee, 'thumb-sm mr-3', 'avatar-title bg-soft-primary text-primary rounded-circle');
                    $('<div>', { class: 'media-body' })
                        .append($('<h6>', { class: 'm-0', text: employee.name }))
                        .append($('<span>', { class: 'text-muted font-12', text: employee.detail }))
                        .appendTo($person);
                    const $button = $('<button>', {
                        type: 'button', class: 'btn btn-sm px-3 flex-shrink-0',
                        'data-picker-toggle': id,
                        'aria-label': `${$root.data('add-label')} / ${$root.data('remove-label')}: ${employee.name}`,
                    });
                    updateButton($button, id);

                    return $row.append($person, $button)[0];
                };

                const resetResults = function () {
                    employees = new Map();
                    page = 0;
                    hasMore = true;
                    $list.empty();
                    $scroll.scrollTop(0);
                    $error.addClass('d-none');
                };

                const loadNextPage = function () {
                    if (request || !hasMore) {
                        return;
                    }

                    const currentRevision = revision;
                    const targetPage = page + 1;
                    $error.addClass('d-none');
                    $loading.removeClass('d-none');
                    $root.attr('aria-busy', 'true');

                    request = $.ajax({
                        url: $root.data('url'),
                        dataType: 'json',
                        data: { search, page: targetPage, exclude_ids: excludedIds },
                    }).done(function (response) {
                        if (currentRevision !== revision) {
                            return;
                        }

                        page = targetPage;
                        hasMore = Boolean(response.has_more);
                        // Append thêm page mới, không render lại các page đã tải trước đó.
                        const rows = (response.data || []).map(function (employee) {
                            const id = Number(employee.employee_id);
                            employees.set(id, employee);

                            return renderEmployee(employee, id);
                        });
                        $list.append(rows);

                        if (page === 1 && !employees.size) {
                            $('<p>', { class: 'text-center text-muted py-3', text: $root.data('empty-label') }).appendTo($list);
                        }
                    }).fail(function (response, status) {
                        if (currentRevision !== revision || status === 'abort') {
                            return;
                        }
                        $error.find('[data-picker-error-message]').text($root.data('error-label'));
                        $error.removeClass('d-none');
                    }).always(function () {
                        if (currentRevision === revision) {
                            request = null;
                            $loading.addClass('d-none');
                            $root.attr('aria-busy', 'false');
                        }
                    });
                };

                // Search mới luôn bắt đầu lại từ page 1 và xóa kết quả search cũ.
                const startSearch = function () {
                    cancelRequest();
                    resetResults();
                    loadNextPage();
                };

                $search.on('input', function () {
                    const nextSearch = normalize(this.value);
                    if (nextSearch === search) {
                        return;
                    }

                    search = nextSearch;
                    cancelRequest();
                    // Chặn scroll gửi request với search mới trước khi debounce kết thúc.
                    hasMore = false;
                    timer = setTimeout(startSearch, 250);
                }).on('keydown', function (event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        clearTimeout(timer);
                        startSearch();
                    }
                });
                $scroll.on('scroll', function () {
                    const distanceToBottom = this.scrollHeight - this.scrollTop - this.clientHeight;

                    // Tải sớm một chút để người dùng không phải chạm chính xác đáy danh sách.
                    if (distanceToBottom <= 60) {
                        loadNextPage();
                    }
                });
                $root.find('[data-picker-retry]').on('click', loadNextPage);
                $list.on('click', '[data-picker-toggle]', function () {
                    const id = Number($(this).attr('data-picker-toggle'));
                    // Toggle chỉ thay đổi state tạm thời; backend vẫn validate lúc submit form.
                    if (selection.has(id)) {
                        selection.delete(id);
                    } else {
                        const employee = employees.get(id);

                        if (!employee) {
                            return;
                        }

                        selection.set(id, employee);
                    }
                    updateButton($(this), id);
                    renderSelection();
                });

                return {
                    // selected là dữ liệu cần khôi phục sau validation error; exclude là ID đã có trong Team.
                    open: function (selected = [], exclude = []) {
                        selection = new Map(selected.map((employee) => [Number(employee.employee_id), employee]));
                        excludedIds = exclude;
                        search = '';
                        $search.val('');
                        renderSelection();
                        startSearch();
                    },
                    // Modal đóng thì hủy request/timer để response đến muộn không cập nhật UI ẩn.
                    close: cancelRequest,
                    // Caller dùng danh sách này để tạo hidden input hoặc thêm row member vào form.
                    selected: () => Array.from(selection.values()),
                };
            };

            window.TeamEmployeePicker = { create, appendAvatar };
        })();
    </script>
@endpush
