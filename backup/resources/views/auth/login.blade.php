@extends('layouts.auth')

@section('content')

<form class="custom-form mt-4 pt-2" method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email or Username</label>
        <input type="text" tabindex="1" class="form-control" name="login" value="{{ old('email') }}" id="username" placeholder="Enter username">
    </div>
    <div class="mb-3">
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                <label class="form-label">Password</label>
            </div>
            <div class="flex-shrink-0">
                <div class="">
                    <a href="" class="text-muted">Forgot password?</a>
                </div>
            </div>
        </div>

        <div class="input-group auth-pass-inputgroup">
            <input type="password" class="form-control" name="password" tabindex="2" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
            <button class="btn btn-light ms-0" type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
        </div>
    </div>
    <div class="row mb-4">
        <div class="col">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember-check">
                <label class="form-check-label" for="remember-check">
                    Remember me
                </label>
            </div>
        </div>

    </div>
    <div class="mb-3">
        <button class="btn btn-primary w-100 waves-effect waves-light">Log In</button>
    </div>
</form>

@endsection