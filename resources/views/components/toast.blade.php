@foreach (['success', 'error', 'warning', 'info'] as $type)
    @if (session($type))
        <script>
            toastr.{{ $type }}(@json(session($type)));
        </script>
    @endif
@endforeach

@if ($errors->any())
    <script>
        @foreach ($errors->all() as $error)
            toastr.error(@json($error), '', { escapeHtml: true });
        @endforeach
    </script>
@endif
