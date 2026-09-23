@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Toast</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Plugins</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Toast</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Basic</h6>
                        <div class="toastr-examples">
                            <div class="row">
                                <div class="col-md-3 text-center">
                                    <a href="#" class="btn btn-success btn-notify-success m-t-b-20">Success</a>
                                </div>
                                <div class="col-md-3 text-center">
                                    <a href="#" class="btn btn-danger btn-notify-danger m-t-b-20">Danger</a>
                                </div>
                                <div class="col-md-3 text-center">
                                    <a href="#" class="btn btn-info btn-notify-info m-t-b-20">Info</a>
                                </div>
                                <div class="col-md-3 text-center">
                                    <a href="#" class="btn btn-warning btn-notify-warning m-t-b-20">Warning</a>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">toastr.options = {
        timeOut: 3000,
        progressBar: true,
        showMethod: "slideDown",
        hideMethod: "slideUp",
        showDuration: 200,
        hideDuration: 200
    };

toastr.success('Successfully completed');

toastr.error('Something went wrong');

toastr.info('This is an informational message');

toastr.warning('You are currently not authorized');</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Other Demos</h6>
                        You can visit the web page to see other examples.
                        <a target="_blank" href="https://codeseven.github.io/toastr/demo.html">Toastr</a>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Toast examples -->
    <script src="{{ url('assets/js/examples/toast.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
