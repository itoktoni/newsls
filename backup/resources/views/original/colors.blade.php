@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Colors</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Colors</li>
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
                        <div class="row">
                            <!-- primary -->
                            <div class="col-md-4">
                                <div class="card card-body bg-primary">
                                    .bg-primary
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-primary-bright">
                                    .bg-primary-bright
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-primary-gradient">
                                    .bg-primary-gradient
                                </div>
                            </div>
                            <!-- secondary -->
                            <div class="col-md-4">
                                <div class="card card-body bg-secondary">
                                    .bg-secondary
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-secondary-bright">
                                    .bg-secondary-bright
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-secondary-gradient">
                                    .bg-secondary-gradient
                                </div>
                            </div>
                            <!-- success -->
                            <div class="col-md-4">
                                <div class="card card-body bg-success">
                                    .bg-success
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-success-bright">
                                    .bg-success-bright
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-success-gradient">
                                    .bg-success-gradient
                                </div>
                            </div>
                            <!-- danger -->
                            <div class="col-md-4">
                                <div class="card card-body bg-danger">
                                    .bg-danger
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-danger-bright">
                                    .bg-danger-bright
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-danger-gradient">
                                    .bg-danger-gradient
                                </div>
                            </div>
                            <!-- warning -->
                            <div class="col-md-4">
                                <div class="card card-body bg-warning">
                                    .bg-warning
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-warning-bright">
                                    .bg-warning-bright
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-warning-gradient">
                                    .bg-warning-gradient
                                </div>
                            </div>
                            <!-- info -->
                            <div class="col-md-4">
                                <div class="card card-body bg-info">
                                    .bg-info
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-info-bright">
                                    .bg-info-bright
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card card-body bg-info-gradient">
                                    .bg-info-gradient
                                </div>
                            </div>
                            <!-- other -->
                            <div class="col-md-4">
                                <div class="card card-body bg-light">
                                    .bg-light
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-dark">
                                    .bg-dark
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-facebook">
                                    .bg-facebook
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-google">
                                    .bg-google
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-instagram">
                                    .bg-instagram
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-linkedin">
                                    .bg-linkedin
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-twitter">
                                    .bg-twitter
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-whatsapp">
                                    .bg-whatsapp
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-dribbble">
                                    .bg-dribbble
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="card card-body bg-youtube">
                                    .bg-youtube
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
