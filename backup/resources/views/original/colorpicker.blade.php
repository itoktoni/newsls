@extends('layouts.app')

@section('head')
    <!-- Colorpicker -->
    <link rel="stylesheet" href="{{ url('vendors/colorpicker/css/bootstrap-colorpicker.min.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Colorpicker</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Forms</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Colorpicker</li>
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
                            Bootstrap Colorpicker is a modular color picker plugin for Bootstrap 4. <a
                                href="https://github.com/itsjavi/bootstrap-colorpicker" class="link-1"
                                target="_blank">Plugin page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Css --&gt;
&lt;link rel="stylesheet" href="vendors/colorpicker/css/bootstrap-colorpicker.min.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/colorpicker/js/bootstrap-colorpicker.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Hex</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" class="sample-selector-hex form-control"
                                           placeholder="#a32a49">
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
  &lt;input type="text" class="colorpicker-example form-control"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input.colorpicker-example').colorpicker({
  format: 'hex'
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Rgb</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" class="sample-selector-rgb form-control"
                                           placeholder="rgb(166,89,89)">
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
  &lt;input type="text" class="colorpicker-example form-control"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input.colorpicker-example').colorpicker({
  format: 'rgb'
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Rgba</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" class="sample-selector-rgba form-control"
                                           placeholder="rgba(102,48,48,1)">
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
  &lt;input type="text" class="colorpicker-example form-control"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input.colorpicker-example').colorpicker({
  format: 'rgba'
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">With custom options</h6>
                        <p>Sample overriding the initial color and format</p>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="input-group sample-selector">
                                    <input type="text" value="red" class="form-control"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text"><i></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="input-group colorpicker-example"&gt;
  &lt;input type="text" value="red" class="form-control"/&gt;
  &lt;div class="input-group-append"&gt;
    &lt;span class="input-group-text"&gt;&lt;i&gt;&lt;/i&gt;&lt;/span&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code
                                        class="language-js">$('input.colorpicker-example').colorpicker();</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Working with events</h6>
                        <a href="#" class="btn btn-dark sample-selector-2">Change background color</a>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;a href="#" class="btn btn-dark colorpicker-example"&gt;Change background color&lt;/a&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('.colorpicker-example').colorpicker().on('changeColor', function (e) {
	$('body')[0].style.backgroundColor = e.color.toString('rgba');
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Transparent color support</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="sample-selector-3 form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
  &lt;input type="text" class="colorpicker-example form-control"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input.colorpicker-example').colorpicker({
    color: "transparent",
    format: "hex"
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Horizontal mode</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="sample-selector-4 form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
  &lt;input type="text" class="colorpicker-example form-control"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input.colorpicker-example').colorpicker({
    horizontal: true
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Aliased color palette</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" class="sample-selector-5 form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
  &lt;input type="text" class="colorpicker-example form-control"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input.colorpicker-example').colorpicker({
    colorSelectors: {
        'black': '#000000',
        'white': '#ffffff',
        'red': '#FF0000',
        'default': '#777777',
        'primary': '#337ab7',
        'success': '#5cb85c',
        'info': '#5bc0de',
        'warning': '#f0ad4e',
        'danger': '#d9534f'
    }
});</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Colorpicker -->
    <script src="{{ url('vendors/colorpicker/js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/colorpicker.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
