@extends('layouts.app')

@section('head')
    <!-- Tour -->
    <link rel="stylesheet" href="{{ url('vendors/enjoyhint/enjoyhint.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Page Tour</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Plugins</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Page Tour</li>
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
                        <h6 class="card-title">Tour</h6>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Earum facere illo, ipsam odit
                            placeat quasi quidem! Architecto itaque quis ratione sint ullam? Amet culpa eum id
                            maiores pariatur rerum, ut.</p>
                        <a href="#" class="btn btn-primary tour">Start Tour</a>

                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Css --&gt;
&lt;link rel="stylesheet" href="vendors/enjoyhint/enjoyhint.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/enjoyhint/enjoyhint.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;a href="#" class="btn btn-primary tour"&gt;Start Tour&lt;/a&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$(document).on('click', 'a.tour', function(){
    var enjoyhint_instance = new EnjoyHint({});

    enjoyhint_instance.set([
        {
            'next .header': 'Quick toolbar.',
        },
        {
            'next .navigation': 'Navigation to navigate the page.',
        }
    ]);
    enjoyhint_instance.run();

    return false;
});</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- Tour -->
    <script src="{{ url('vendors/enjoyhint/enjoyhint.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/tour.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
