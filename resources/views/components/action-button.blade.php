@props([
    'type' => 'primary',
    'icon' => null,
    'href' => null,
])

@if($href)
    <a href="{{ $href }}" class="btn btn-outline-{{ $type }} waves-effect waves-light mr-2 btn-sm">
        @else
            <button class="btn btn-outline-{{ $type }} waves-effect waves-light mr-2 btn-sm">
                @endif
                @if($icon)
                <i class="{{ $icon }} me-1"></i>
        @endif

        {{ $slot }}

        @if($href)
    </a>
    @else
        </button>
@endif