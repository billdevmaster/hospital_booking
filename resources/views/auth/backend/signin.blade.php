@extends('layouts.backend.auth.auth')
@section('content')
<!-- Login-->
<div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5">
    <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
        <h2 class="card-title font-weight-bold mb-1">Sisene BOOKid haldusesse! </h2>
        @if ($errors->has('fail'))
            <div class="alert alert-danger">
                <div class="alert-body">
                    Kasutajat ei leitud.
                </div>
            </div>
        @endif
        <form class="auth-login-form mt-2" action="{{ route('backend.signin') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="login-email">Email</label>
                <input class="form-control" id="login-email" type="text" name="login-email" placeholder="john@example.com" aria-describedby="login-email" autofocus="" tabindex="1" />
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-between">
                    <label for="login-password">Parool</label><a href="page-auth-forgot-password-v2.html"><small>Unustasid parooli?</small></a>
                </div>
                <div class="input-group input-group-merge form-password-toggle">
                    <input class="form-control form-control-merge" id="login-password" type="password" name="login-password" placeholder="············" aria-describedby="login-password" tabindex="2" />
                    <div class="input-group-append"><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span></div>
                </div>
            </div>
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input class="custom-control-input" id="remember-me" name="login-remember" type="checkbox" tabindex="3" />
                    <label class="custom-control-label" for="remember-me"> Pea meeles</label>
                </div>
            </div>
            <button class="btn btn-primary btn-block" tabindex="4">Logi sisse</button>
        </form>
        <p class="text-center mt-2"><span>Sul ei ole veel kontot?</span><a href="{{ route("signup") }}"><span>&nbsp;Loo konto</span></a></p>
    </div>
</div>
<!-- /Login-->
@endsection