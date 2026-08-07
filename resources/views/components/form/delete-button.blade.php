@props([
    'action'
])
@php
    use Illuminate\Support\Str;
    $formId = 'delete-form-' . Str::uuid();
@endphp
<form
        id="{{ $formId }}"
        action="{{ $action }}"
        method="POST"
        class="d-inline"
>
    @csrf
    @method('DELETE')
    <button
            style="width: 34px"
            type="button"
            class="btn btn-sm btn-outline-danger"
            onclick="confirmDelete('{{ $formId }}')"
            title="{{ __('common.button.delete') }}"
    >
        <i class="fas fa-trash"></i>
    </button>
</form>