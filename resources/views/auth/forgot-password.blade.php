<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>ERP | {{ __('common.account.reset_password') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="ERP | {{ __('common.account.reset_password') }}" name="description" />
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
                                <h4 class="mt-0 mb-3 mt-5">{{ __('common.account.reset_password') }}</h4>
                                <p class="text-muted mb-0">{{ __('common.account.enter_email') }}</p>
                            </div> <!--end auth-logo-text-->
                            <form class="form-horizontal auth-form my-4" action="{{ route('account.send-reset-link') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="email">{{ __('site.employees.email') }}</label>
                                    <div class="input-group mb-3">
                                        <span class="auth-form-icon">
                                            <i data-feather="mail" class="icon-xs"></i>
                                        </span>
                                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Email address">
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
                                @if (session('success'))
                                    <div class="alert icon-custom-alert alert-outline-success fade show" role="alert">
                                        <div class="alert-text">
                                            <span class="text-sm">{{ session('success') }}</span>
                                        </div>
                                        <div class="alert-close">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true"><i class="mdi mdi-close text-success"></i></span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group mb-0 row">
                                    <div class="col-12 mt-2">
                                        <button class="btn btn-gradient-primary btn-round btn-block waves-effect waves-light" type="submit">{{ __('common.button.reset') }} <i class="fas fa-sign-in-alt ml-1"></i></button>
                                    </div><!--end col-->
                                </div> <!--end form-group-->
                            </form><!--end form-->
                        </div><!--end /div-->
                        <div class="m-3 text-center text-muted">
                            <p class="">{{ __('common.account.remember_it') }}  <a href="{{ route('login') }}" class="text-primary ml-2">{{ __('common.account.sign_in_here') }}</a></p>
                        </div>
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
<script src="{{ asset('js/toastr.min.js') }}"></script>
<script src="{{ asset('js/jquery.slimscroll.min.js') }}"></script>
<script>
    feather.replace()
</script>
</body>
</html>