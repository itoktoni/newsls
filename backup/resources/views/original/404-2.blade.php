@extends('layouts.app')

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Page not found!</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Error</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">404</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <p>We couldn't find the page you were looking for. Please try looking for something different.</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search"
                                       aria-label="Example text with button addon" aria-describedby="button-addon1">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="button-addon1">
                                        <i data-feather="search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary">
                            <i data-feather="search" class="mr-2"></i> Search
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
