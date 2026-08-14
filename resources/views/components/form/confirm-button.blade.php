@props([
    'action',
    'method' => 'POST',
    'title' => __('common.messages.confirm'),
    'text' => null,
    'confirmText' => __('common.button.confirm'),
    'cancelText' => __('common.button.cancel'),
    'icon' => 'fas fa-check',
    'class' => 'btn btn-sm btn-outline-primary',
    'label' => null,
])

<form action="{{ $action }}" method="POST" class="d-inline" >
    @csrf
    @if (strtoupper($method) !== 'POST')
        @method($method)
    @endif
    <button
            type="button"
            class="{{ $class }}"
            title="{{ $label }}"
            data-confirm
            data-confirm-title="{{ $title }}"
            data-confirm-text="{{ $text }}"
            data-confirm-button="{{ $confirmText }}"
            data-cancel-button="{{ $cancelText }}"
    >
        <i class="{{ $icon }}"></i> {{ $label }}
    </button>
</form>