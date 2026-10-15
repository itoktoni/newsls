<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Bordash - Admin Dashboard Template</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url('assets/media/image/favicon.png') }}"/>

    <!-- Plugin styles -->
    <link rel="stylesheet" href="{{ url('vendors/bundle.css') }}" type="text/css">

    <!-- App styles -->
    <link rel="stylesheet" href="{{ url('assets/css/app.min.css') }}" type="text/css">
</head>
<body class="form-membership">

<div class="form-wrapper">

    <!-- logo -->
    <div id="logo">
        <img class="logo" src="{{ url('assets/media/image/logo.png') }}" alt="image">
        <img class="logo-dark" src="{{ url('assets/media/image/logo-dark.png') }}" alt="image">
    </div>
    <!-- ./ logo -->

    <h5>Enter your password</h5>

    <!-- form -->
    <form>
        <div class="form-group d-flex align-items-center">
            <div class="mr-3">
                <figure class="mb-4 avatar avatar-sm">
                    <img src="{{ url('assets/media/image/user/man_avatar3.jpg') }}" class="rounded-circle" alt="avatar">
                </figure>
            </div>
            <input type="password" class="form-control" placeholder="Password" required autofocus>
        </div>
        <button class="btn btn-primary btn-block">Sign in</button>
        <hr>
        <a href="{{ route('pages.login') }}" class="btn btn-sm btn-outline-light ml-1">Sign out</a>
    </form>
    <!-- ./ form -->

</div>

<!-- Plugin scripts -->
<script src="{{ url('vendors/bundle.js') }}"></script>

<!-- App scripts -->
<script src="{{ url('assets/js/app.min.js') }}"></script>
</body>
</html>
