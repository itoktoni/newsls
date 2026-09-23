@extends('layouts.app')

@section('head')
    <!-- Clockpicker -->
    <link rel="stylesheet" href="{{ url('vendors/clockpicker/bootstrap-clockpicker.min.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Timepicker</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Forms</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Timepicker</li>
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
                            A clock-style timepicker for Bootstrap (or jQuery). <a
                                href="http://weareoutman.github.io/clockpicker/" class="link-1" target="_blank">Plugin
                                page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Css --&gt;
&lt;link rel="stylesheet" href="vendors/clockpicker/bootstrap-clockpicker.min.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/clockpicker/bootstrap-clockpicker.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Basic Usage</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group clockpicker-demo">
                                    <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-clock-o"></i>
                                                </span>
                                    </div>
                                    <input type="text" class="form-control" value="09:30">
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="input-group clockpicker-example"&gt;
  &lt;div class="input-group-prepend"&gt;
    &lt;span class="input-group-text"&gt;
      &lt;i class="fa fa-clock-o"&gt;&lt;/i&gt;
    &lt;/span&gt;
  &lt;/div&gt;
  &lt;input type="text" class="form-control" value="09:30"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('.clockpicker-example').clockpicker({
    donetext: 'Done'
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Auto Close</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group clockpicker-autoclose-demo">
                                    <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-clock-o"></i>
                                            </span>
                                    </div>
                                    <input type="text" class="form-control" value="18:30">
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="input-group clockpicker-example"&gt;
  &lt;div class="input-group-prepend"&gt;
    &lt;span class="input-group-text"&gt;
      &lt;i class="fa fa-clock-o"&gt;&lt;/i&gt;
    &lt;/span&gt;
  &lt;/div&gt;
  &lt;input type="text" class="form-control" value="18:30"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('.clockpicker-example').clockpicker({
    autoclose: true
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Set Default Value</h6>
                        <p>Set default value, input without addon, and manual operations</p>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group clockpicker-minutes-demo">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-primary" type="button" id="check-minutes">Check the
                                            minutes
                                        </button>
                                    </div>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="input-group clockpicker-example"&gt;
  &lt;div class="input-group-prepend"&gt;
    &lt;button class="btn btn-primary" type="button" id="check-minutes"&gt;Check the minutes
    &lt;/button&gt;
  &lt;/div&gt;
  &lt;input type="text" class="form-control"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">let input = $('.clockpicker-example').clockpicker({
    placement: 'bottom',
    align: 'left',
    autoclose: true,
    'default': 'now'
});

$(document).on('click', '#check-minutes', function (e) {
    e.stopPropagation();
    input.clockpicker('show').clockpicker('toggleView', 'minutes');
});</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Clockpicker -->
    <script src="{{ url('vendors/clockpicker/bootstrap-clockpicker.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/clockpicker.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
