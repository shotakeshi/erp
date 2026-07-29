<!DOCTYPE html>
<html lang="vi">
    @include('layouts.components.head')
<body>
@include('layouts.components.left-bar')
@include('layouts.components.topbar')
<div class="page-wrapper">
    <div class="page-content-tab">
        <div class="container-fluid">
            @yield('content')
        </div>
        @include('layouts.components.right-bar')
        @include('layouts.components.footer')
    </div>
</div>
@include('layouts.components.preloader')
@include('layouts.components.scripts')
</body>
</html>