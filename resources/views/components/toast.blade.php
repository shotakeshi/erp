@if(session('success'))
    <script>
        toastr.success(@json(session('success')));
    </script>
@endif

@if(session('error'))
    <script>
        toastr.error(@json(session('error')));
    </script>
@endif

@if(session('warning'))
    <script>
        toastr.warning(@json(session('warning')));
    </script>
@endif

@if(session('info'))
    <script>
        toastr.info(@json(session('info')));
    </script>
@endif