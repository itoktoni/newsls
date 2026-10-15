@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Tooltip</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tooltip</li>
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
                        <h6 class="card-title">Default Tooltip</h6>
                        <button type="button" class="btn btn-secondary" title="Default Tooltip"
                                data-toggle="tooltip">
                            Tooltip Button
                        </button>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;button type="button" class="btn btn-secondary" title="Default tooltip" data-toggle="tooltip"&gt;
  ...
&lt;/button&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Tooltip Placement</h6>
                        <div class="row text-center justify-content-md-center">
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary m-r-5" data-toggle="tooltip"
                                        data-placement="top" title="Tooltip on top">
                                    Tooltip on top
                                </button>
                            </div>
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary m-r-5" data-toggle="tooltip"
                                        data-placement="right" title="Tooltip on right">
                                    Tooltip on right
                                </button>
                            </div>
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary m-r-5" data-toggle="tooltip"
                                        data-placement="bottom" title="Tooltip on bottom">
                                    Tooltip on bottom
                                </button>
                            </div>
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary m-r-5" data-toggle="tooltip"
                                        data-placement="left" title="Tooltip on left">
                                    Tooltip on left
                                </button>
                            </div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;button type="button" class="btn btn-secondary" title="Default tooltip" data-toggle="tooltip" data-placement="top"&gt;
  ...
&lt;/button&gt;</code></pre>
                        </div>
                        <table class="table table-bordered">
                            <tr>
                                <th>Attr</th>
                                <th>Keys</th>
                            </tr>
                            <tr>
                                <td style="width: 30%">data-placement="*"</td>
                                <td>top | right | bottom | left</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Tooltip HTML</h6>
                        <div class="col-md-3 m-t-b-20">
                            <button type="button" class="btn btn-secondary" data-toggle="tooltip"
                                    data-html="true" title="<em>Tooltip</em> <u>with</u> <b>HTML</b>">
                                Tooltip with HTML
                            </button>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;button type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="&lt;em&gt;Tooltip&lt;/em&gt; &lt;u&gt;with&lt;/u&gt; &lt;b&gt;HTML&lt;/b&gt;"&gt;
  ...
&lt;/button&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Disabled Element</h6>
                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled tooltip">
                              <button class="btn btn-secondary" style="pointer-events: none;" type="button" disabled>Disabled button</button>
                            </span>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Disabled tooltip"&gt;
  &lt;button class="btn btn-primary" style="pointer-events: none;" type="button" disabled&gt;
  ...
  &lt;/button&gt;
&lt;/span&gt;</code></pre>
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
