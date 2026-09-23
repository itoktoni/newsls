@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Chartjs</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Charts</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Chartjs</li>
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
                        <p class="lead">
                            Chart.js is a popular open source library that helps us to plot data in web
                            applications. <a href="https://www.chartjs.org/" class="link-1" target="_blank">Plugin
                                page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                            <pre><code class="language-html">&lt;script src="vendors/charts/chartjs/chart.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <p>We prepared demo samples. You don't have to use them. You can create your own demos.</p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                            <pre><code class="language-html">&lt;script src="assets/js/examples/charts/chartjs.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <canvas id="chartjs_one"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <canvas id="chartjs_two"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <canvas id="chartjs_three"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <canvas id="chartjs_four"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <canvas id="chartjs_five"></canvas>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <canvas id="chartjs_six"></canvas>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Chartjs -->
    <script src="{{ url('vendors/charts/chartjs/chart.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/charts/chartjs.js') }}"></script>
    <div class="colors"> <!-- To use theme colors with Javascript -->
        <div class="bg-primary"></div>
        <div class="bg-primary-bright"></div>
        <div class="bg-secondary"></div>
        <div class="bg-secondary-bright"></div>
        <div class="bg-info"></div>
        <div class="bg-info-bright"></div>
        <div class="bg-success"></div>
        <div class="bg-success-bright"></div>
        <div class="bg-danger"></div>
        <div class="bg-danger-bright"></div>
        <div class="bg-warning"></div>
        <div class="bg-warning-bright"></div>
    </div>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
