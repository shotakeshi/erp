{{--
|--------------------------------------------------------------------------
| Inline Editable Component (x-editable)
|--------------------------------------------------------------------------
|
| Renders the slot as a click-to-edit text value that is saved by AJAX.
| The endpoint must return JSON (`{ message, value? }`), see public/js/shared/x-editable.js.
|
| Usage:
| <x-form.editable
|     name="role"
|     :pk="$assignment->id"
|     :url="route('projects.assignments.update', [$project, $employee])"
|     :title="__('site.projects.role')"
|     :maxlength="50"
| >{{ $assignment->role }}</x-form.editable>
|
| Page requirements:
| @push('css')     plugins/x-editable/css/bootstrap-editable.css
| @push('scripts') plugins/x-editable/js/bootstrap-editable.min.js and js/shared/x-editable.js
--}}
@props([
    'name',
    'pk',
    'url',
    'title' => null,
    'method' => 'PUT',
    'maxlength' => null,
])
<a
    href="#"
    {{ $attributes->class(['editable', 'editable-click']) }}
    data-x-editable
    data-type="text"
    data-name="{{ $name }}"
    data-pk="{{ $pk }}"
    data-url="{{ $url }}"
    data-method="{{ $method }}"
    data-title="{{ $title }}"
    data-emptytext="-"
    @if ($maxlength) data-maxlength="{{ $maxlength }}" @endif
    data-csrf="{{ csrf_token() }}"
    data-error-label="{{ __('common.messages.update_failed') }}"
>{{ trim($slot) }}</a>
