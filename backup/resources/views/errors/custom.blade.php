<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title') - {{ env('APP_NAME') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url('assets/media/image/favicon.png') }}"/>

    <!-- App styles -->
    <link rel="stylesheet" href="{{ url('assets/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ url('assets/css/app.min.css') }}" type="text/css">
</head>
<body class="error-page bg-white">

    <div class="my-5 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center mb-5">
                        <h3 class="fw-semibold">
                         Line {{ $exception->getLine() }}
                        </h3>
                        <h5>
                            <code>{{ $exception->getMessage() ?: 'Service Unavailable' }}</code>
                        </h5>
                        <div class="mt-5 text-center">
                            <a class="btn btn-primary waves-effect waves-light" href="{{ url('/login') }}">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-10 col-xl-8">
                    <div>
                        <img src="assets/images/error-img.png" alt="" class="img-fluid">
                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>

</body>
</html>

