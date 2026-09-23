@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Justgage</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Charts</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Justgage</li>
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
                            JustGage is a handy JavaScript plugin for generating and animating nice & clean gauges.
                            <a href="https://justgage.com/" class="link-1" target="_blank">Plugin page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;script src="vendors/charts/justgage/raphael-2.1.4.min.js"&gt;&lt;/script&gt;
&lt;script src="vendors/charts/justgage/justgage.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <p>We prepared demo samples. You don't have to use them. You can create your own demos.</p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                            <pre><code class="language-html">&lt;script src="assets/js/examples/charts/justgage.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 col-sm-12">
                                <div id="justgage_one" style="height:150px"></div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div id="justgage_two" style="height:150px"></div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div id="justgage_three" style="height:150px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 col-sm-12">
                                <div id="justgage_four" style="height:150px"></div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div id="justgage_five" style="height:150px"></div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div id="justgage_six" style="height:150px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="justgage_seven" style="height:300px"></div>
                            </div>
                            <div class="col-md-6">
                                <div id="justgage_eight" style="height:300px"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="justgage_nine" style="height:300px"></div>
                            </div>
                            <div class="col-md-6">
                                <div id="justgage_ten" style="height:300px"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Justgage -->
    <script src="{{ url('vendors/charts/justgage/raphael-2.1.4.min.js') }}"></script>
    <script src="{{ url('vendors/charts/justgage/justgage.js') }}"></script>
    <script src="{{ url('assets/js/examples/charts/justgage.js') }}"></script>
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
