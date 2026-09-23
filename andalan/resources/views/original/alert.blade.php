@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Alerts</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Alerts</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Default Functionality</h6>

                <div class="alert alert-secondary" role="alert">
                    A simple secondary alert—check it out!
                </div>
                <div class="alert alert-success" role="alert">
                    A simple success alert—check it out!
                </div>
                <div class="alert alert-danger" role="alert">
                    A simple danger alert—check it out!
                </div>
                <div class="alert alert-warning" role="alert">
                    A simple warning alert—check it out!
                </div>
                <div class="alert alert-info" role="alert">
                    A simple info alert—check it out!
                </div>
                <div class="alert alert-dark m-b-0" role="alert">
                    A simple dark alert—check it out!
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="alert alert-secondary" role="alert"&gt;...&lt;/div&gt;
&lt;div class="alert alert-success" role="alert"&gt;...&lt;/div&gt;
&lt;div class="alert alert-danger" role="alert"&gt;...&lt;/div&gt;
&lt;div class="alert alert-warning" role="alert"&gt;...&lt;/div&gt;
&lt;div class="alert alert-info" role="alert"&gt;...&lt;/div&gt;
&lt;div class="alert alert-dark m-b-0" role="alert"&gt;...&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Alert Link</h6>
                <div class="alert alert-success" role="alert">
                    A simple success alert with <a href="#" class="alert-link">an example link</a>. Give it a
                    click if you like.
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="alert alert-success" role="alert"&gt;
  A simple success alert with &lt;a href="#" class="alert-link"&gt;an example link&lt;/a&gt;. Give it a
  click if you like.
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Additional Content</h6>

                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">Well done!</h4>
                    <p>Aww yeah, you successfully read this important alert message. This example text is going to run a
                        bit longer so that you can see how spacing within an alert works with this kind of content.</p>
                    <hr>
                    <p class="mb-0">Whenever you need to, be sure to use margin utilities to keep things nice and
                        tidy.</p>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="alert alert-success" role="alert"&gt;
  &lt;h4 class="alert-heading"&gt;Well done!&lt;/h4&gt;
  &lt;p&gt;Aww yeah, you successfully read this important alert message. This example text is going to run a bit longer so that you can see how spacing within an alert works with this kind of content.&lt;/p&gt;
  &lt;hr&gt;
  &lt;p class="mb-0"&gt;Whenever you need to, be sure to use margin utilities to keep things nice and tidy.&lt;/p&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Dismissing</h6>

                <div class="alert alert-danger alert-dismissible" role="alert">
                    A simple danger alert—check it out!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="ti-close"></i>
                    </button>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="alert alert-danger alert-dismissible" role="alert"&gt;
A simple danger alert—check it out!
  &lt;button type="button" class="close" data-dismiss="alert" aria-label="Close"&gt;
    &lt;i class="ti-close"&gt;&lt;/i&gt;
  &lt;/button&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Alert Icon</h6>

                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="ti-check mr-2"></i> A simple success alert—check it out!
                </div>

                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="ti-help-alt mr-2"></i> A simple warning alert—check it out!
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="alert alert-success d-flex align-items-center" role="alert"&gt;
  &lt;i class="ti-check mr-2"&gt;&lt;/i&gt; A simple success alert—check it out!
&lt;/div&gt;

&lt;div class="alert alert-warning d-flex align-items-center" role="alert"&gt;
  &lt;i class="ti-help-alt mr-2"&gt;&lt;/i&gt; A simple warning alert—check it out!
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Border Style</h6>

                <div class="alert alert-info alert-with-border" role="alert">
                    A simple info alert—check it out!
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                    <pre><code class="language-html">&lt;div class="alert alert-info alert-with-border" role="alert"&gt;...&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
