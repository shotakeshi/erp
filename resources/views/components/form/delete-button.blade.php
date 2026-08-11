@props([
    'action',
    'title' => null
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
            @if(empty($title)) style="width: 34px" @endif
            type="button"
            class="btn btn-sm btn-outline-danger"
            onclick="confirmDelete('{{ $formId }}')"
            title="{{ __('common.button.delete') }}"
    >
        <i class="fas fa-trash"></i> {{ $title }}
    </button>
</form>