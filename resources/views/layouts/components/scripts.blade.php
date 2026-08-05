<!-- jQuery  -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/metismenu.min.js') }}"></script>
<script src="{{ asset('js/waves.js') }}"></script>
<script src="{{ asset('js/feather.min.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('plugins/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('js/toastr.min.js') }}"></script>
@stack('scripts')
<!-- App js -->
<script src="{{ asset('js/app.js') }}?v={{ time() }}"></script>