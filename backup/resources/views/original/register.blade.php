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

    <h5>Create account</h5>

    <!-- form -->
    <form>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Firstname" required autofocus>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" placeholder="Lastname" required>
        </div>
        <div class="form-group">
            <input type="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" placeholder="Password" required>
        </div>
        <button class="btn btn-primary btn-block">Register</button>
        <hr>
        <p class="text-muted">Already have an account?</p>
        <a href="{{ route('pages.login') }}" class="btn btn-outline-light btn-sm">Sign in!</a>
    </form>
    <!-- ./ form -->

</div>

<!-- Plugin scripts -->
<script src="{{ url('vendors/bundle.js') }}"></script>

<!-- App scripts -->
<script src="{{ url('assets/js/app.min.js') }}"></script>
</body>
</html>
