@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Spinners</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Spinners</li>
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
                        <h6 class="card-title">Border spinner</h6>
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-border text-secondary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-border text-danger" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-border text-warning" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-border text-light" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-border text-dark" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>

                    <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="spinner-border text-primary" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-border text-secondary" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-border text-success" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-border text-danger" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-border text-warning" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-border text-info" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-border text-light" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-border text-dark" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;</code></pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Growing spinner</h6>
                        <div class="spinner-grow text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow text-secondary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow text-danger" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow text-warning" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow text-light" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow text-dark" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="spinner-grow text-primary" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-grow text-secondary" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-grow text-success" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-grow text-danger" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-grow text-warning" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-grow text-info" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-grow text-light" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;
&lt;div class="spinner-grow text-dark" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Alignment</h6>
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="clearfix">
                            <div class="spinner-border float-right" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mt-3">
                            <strong>Loading...</strong>
                            <div class="spinner-border ml-auto" role="status" aria-hidden="true"></div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="spinner-border" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;

&lt;div class="d-flex justify-content-center"&gt;
  &lt;div class="spinner-border" role="status"&gt;
    &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
  &lt;/div&gt;
&lt;/div&gt;

&lt;div class="clearfix"&gt;
  &lt;div class="spinner-border float-right" role="status"&gt;
    &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
  &lt;/div&gt;
&lt;/div&gt;

&lt;div class="d-flex align-items-center"&gt;
  &lt;strong&gt;Loading...&lt;/strong&gt;
  &lt;div class="spinner-border ml-auto" role="status" aria-hidden="true"&gt;&lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Size</h6>
                        <div class="mb-3">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="spinner-grow spinner-grow-sm" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="spinner-grow" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div>
                            <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;!-- sm --&gt;
&lt;div class="spinner-border spinner-border-sm" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;

&lt;!-- lg --&gt;
&lt;div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Buttons</h6>
                        <div class="mb-3">
                            <button class="btn btn-primary mr-2" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status"
                                          aria-hidden="true"></span>
                                <span class="sr-only">Loading...</span>
                            </button>
                            <button class="btn btn-primary" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm m-r-5" role="status"
                                          aria-hidden="true"></span>
                                Loading...
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-primary mr-2" type="button" disabled>
                                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                                <span class="sr-only">Loading...</span>
                            </button>
                            <button class="btn btn-primary" type="button" disabled>
                                    <span class="spinner-grow spinner-grow-sm m-r-5" role="status"
                                          aria-hidden="true"></span>
                                Loading...
                            </button>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button class="btn btn-primary" type="button" disabled&gt;
  &lt;span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"&gt;&lt;/span&gt;
  &lt;span class="sr-only"&gt;Loading...&lt;/span&gt;
&lt;/button&gt;

&lt;button class="btn btn-primary" type="button" disabled&gt;
  &lt;span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"&gt;&lt;/span&gt;
  Loading...
&lt;/button&gt;</code></pre>
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
