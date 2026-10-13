/**
 * Shared employee picker for the selection modal (Team, Project).
 * resources/views/components/employee-picker.blade.php.
 */
(function () {
    // Render the avatar image, or initials when the employee has no photo.
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
        // selection keeps the employees the user has picked
        let selection = new Map();
        // employees holds the employees loaded for the current search.
        let employees = new Map();
        let excludedIds = [];
        let page = 0;
        let hasMore = false;
        let request = null;
        // Increase the revision when a request is cancelled so a stale response cannot overwrite newer results.
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
            const $row = $('<div>', { class: 'd-flex align-items-center justify-content-between mb-3 mr-3' });
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
                // Append the new page without re-rendering pages already loaded.
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

        // A new search always restarts from page 1 and clears the previous results.
        const startSearch = function () {
            cancelRequest();
            resetResults();
            loadNextPage();
        };

        $search.on('input', function () {
            // The backend folds accents (Str::ascii), so only surrounding spaces are ignored here.
            const nextSearch = this.value.trim();
            if (nextSearch === search) {
                return;
            }

            search = nextSearch;
            cancelRequest();
            // Stop scroll from sending a request for the new search before the debounce ends.
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

            // Load slightly early so the user does not have to reach the exact bottom of the list.
            if (distanceToBottom <= 60) {
                loadNextPage();
            }
        });
        $root.find('[data-picker-retry]').on('click', loadNextPage);
        $list.on('click', '[data-picker-toggle]', function () {
            const id = Number($(this).attr('data-picker-toggle'));
            // Toggle only changes temporary state; the backend still validates on form submit.
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
            // selected is data to restore after a validation error; exclude is the IDs hidden from the results.
            open: function (selected = [], exclude = []) {
                selection = new Map(selected.map((employee) => [Number(employee.employee_id), employee]));
                excludedIds = exclude;
                search = '';
                $search.val('');
                renderSelection();
                startSearch();
            },
            // When the modal closes, cancel pending requests and timers so late responses do not update the hidden UI.
            close: cancelRequest,
            // The caller uses this list to create hidden inputs or add member rows to the form.
            selected: () => Array.from(selection.values()),
        };
    };

    window.EmployeePicker = { create, appendAvatar };
})();
