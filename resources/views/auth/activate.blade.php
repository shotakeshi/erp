<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>ERP | Active User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="ERP | Sign In" name="description" />
    <meta content="ERP TEAM" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}">
    <!-- App css -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/metisMenu.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('css/app.min.css') }}" rel="stylesheet" type="text/css" />
</head>
<body class="account-body accountbg">
<!-- Log In page -->
<div class="container">
    <div class="row vh-100 ">
        <div class="col-12 align-self-center">
            <div class="auth-page" >
                <div class="card auth-card shadow-lg">
                    <div class="card-body">
                        <div class="px-3">
                            <div class="auth-logo-box text-center">
                                <a href="javascript:void(0)" class="logo logo-admin"><img src="{{ asset('images/logo-sm.png') }}" height="60" alt="logo" class="auth-logo"></a>
                            </div><!--end auth-logo-box-->
                            <div class="text-center auth-logo-text">
                                <h4 class="mt-0 mb-3 mt-5">{{ __('common.account.activate') }}</h4>
                                <p class="text-muted mb-0">{{ __('common.account.set_password_description') }}</p>
                            </div> <!--end auth-logo-text-->
                            <form class="form-horizontal auth-form my-4" action="{{ route('account.activate.complete', $token) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="password">{{ __('common.account.password') }}</label>
                                    <div class="input-group mb-3">
                                        <span class="auth-form-icon">
                                            <i data-feather="key" class="icon-xs"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••">
                                    </div>
                                </div><!--end form-group-->
                                <div class="form-group">
                                    <label for="password_confirmation">{{ __('common.account.password_confirmation') }}</label>
                                    <div class="input-group mb-3">
                                        <span class="auth-form-icon">
                                            <i data-feather="key" class="icon-xs"></i>
                                        </span>
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••">
                                    </div>
                                </div><!--end form-group-->
                                @if ($errors->any())
                                    <div class="alert icon-custom-alert alert-outline-pink fade show" role="alert">
                                        <div class="alert-text">
                                            @foreach ($errors->all() as $error)
                                                <span class="text-sm">{{ $error }}</span><br>
                                            @endforeach
                                        </div>
                                        <div class="alert-close">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true"><i class="mdi mdi-close text-danger"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group mb-0 row">
                                    <div class="col-12 mt-2">
                                        <button class="btn btn-gradient-primary btn-round btn-block waves-effect waves-light" type="submit">{{ __('common.account.activate') }}</button>
                                    </div><!--end col-->
                                </div> <!--end form-group-->
                            </form><!--end form-->
                        </div><!--end /div-->
                    </div><!--end card-body-->
                </div><!--end card-->
            </div><!--end auth-page-->
        </div><!--end col-->
    </div><!--end row-->
</div><!--end container-->
<!-- End Log In page -->
<!-- jQuery  -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/metismenu.min.js') }}"></script>
<script src="{{ asset('js/waves.js') }}"></script>
<script src="{{ asset('js/feather.min.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
<script>
    feather.replace()
</script>
</body>
</html>