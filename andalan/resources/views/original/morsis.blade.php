@extends('layouts.app')

@section('head')
    <!-- Morsis -->
    <link rel="stylesheet" href="{{ url('vendors/charts/morsis/morris.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Morsis</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Charts</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Morsis</li>
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
                            Morsis is a popular open source library that helps us to plot data in web applications.
                            <a href="https://morrisjs.github.io/morris.js/" class="link-1" target="_blank">Plugin
                                page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Style --&gt;
&lt;link rel="stylesheet" href="vendors/charts/morsis/morris.css" type="text/css"&gt;

&lt;script src="vendors/charts/morsis/raphael-2.1.4.min.js"&gt;&lt;/script&gt;
&lt;script src="vendors/charts/morsis/morris.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <p>We prepared demo samples. You don't have to use them. You can create your own demos.</p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                            <pre><code class="language-html">&lt;script src="assets/js/examples/charts/morsis.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="bar-chart" style="height: 250px;"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="line-chart" style="height: 250px;"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="stacked" style="height: 250px;"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="area-chart" style="height: 250px;"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div id="pie-chart" style="height: 250px;"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Morsis -->
    <script src="{{ url('vendors/charts/morsis/raphael-2.1.4.min.js') }}"></script>
    <script src="{{ url('vendors/charts/morsis/morris.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/charts/morsis.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
