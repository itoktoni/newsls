@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Peity</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Charts</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Peity</li>
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
                            Peity is a simple JQuery plugin that converts the content of an element into graphics.
                            <a href="https://benpickles.github.io/peity/" class="link-1" target="_blank">Plugin
                                page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                            <pre><code class="language-html">&lt;script src="vendors/charts/peity/jquery.peity.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <p>We prepared demo samples. You don't have to use them. You can create your own demos.</p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                            <pre><code class="language-html">&lt;script src="assets/js/examples/charts/peity.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 offset-2">
                                <div class="row">
                                    <div class="col mb-4">
                                        <span class="pie-1">226/360</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-2">0.52/1.561</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-3">1,4</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-4">226,134</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-5">0.52,1.041</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code
                                        class="language-html">&lt;span class="pie"&gt;226/360&lt;/span&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$(".pie").peity("pie", {
    "fill": [black, gray],
    "radius": 30
});</code></pre>
                        </div>
                        <div class="row">
                            <div class="col-md-8 offset-2">
                                <div class="row">
                                    <div class="col mb-4">
                                        <span class="pie-6">226/360</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-7">0.52/1.561</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-8">1,4</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-9">226,134</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="pie-10">0.52,1.041</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code
                                        class="language-html">&lt;span class="pie"&gt;226/360&lt;/span&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$(".pie").peity("pie", {
    "fill": [black, gray],
    "radius": 30,
    "innerRadius": 20
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-8 offset-2">
                                <div class="row">
                                    <div class="col mb-4">
                                        <span class="line-1">2,5,9,6,5,-9,-7,3,7,5</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="line-2">5,3,9,6,5,9,7,3,5,2</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="line-3">-5,3,9,6,5,9,-3,3,5,2</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="line-4">8,3,2,6,5,9,7,3,5,2</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="line-5">8,3,2,6,-5,9,7,3,5,2</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;span class="line"&gt;2,5,9,6,5,-9,-7,3,7,5&lt;/span&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$(".line").peity("line", {
    "fill": gray,
    "stroke": black,
    "height": 80,
    "width": 100
});</code></pre>
                        </div>
                        <div class="row">
                            <div class="col-md-8 offset-2">
                                <div class="row">
                                    <div class="col mb-4">
                                        <span class="bar-1">2,5,9,6,5,-9,-7,3,7,5</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="bar-2">5,3,9,6,5,9,7,3,5,2</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="bar-3">-5,3,9,6,5,9,-3,3,5,2</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="bar-4">8,3,2,6,5,9,7,3,5,2</span>
                                    </div>
                                    <div class="col mb-4">
                                        <span class="bar-5">8,3,2,6,-5,9,7,3,5,2</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;span class="bar"&gt;2,5,9,6,5,-9,-7,3,7,5&lt;/span&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$(".bar").peity("bar", {
    "fill": [black, gray],
    "height": 50,
    "width": 100
});</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Peity -->
    <script src="{{ url('vendors/charts/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/charts/peity.js') }}"></script>

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
