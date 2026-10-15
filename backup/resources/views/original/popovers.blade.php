@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Popovers</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Popovers</li>
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
                        <h6 class="card-title">Basic Overview</h6>
                        <button type="button" class="btn btn-primary" data-toggle="popover" title="Popover title"
                                data-content="And here's some amazing content. It's very engaging. Right?">Click to
                            toggle popover
                        </button>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary" data-toggle="popover" title="Popover title" data-content="And here's some amazing content. It's very engaging. Right?"&gt;
  ...
&lt;/button&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Directions</h6>
                        <div class="row text-center justify-content-md-center">
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary m-r-5" data-container="body"
                                        data-toggle="popover" data-placement="top"
                                        data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                    Popover on top
                                </button>
                            </div>
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary m-r-5" data-container="body"
                                        data-toggle="popover" data-placement="right"
                                        data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                    Popover on right
                                </button>
                            </div>
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary m-r-5" data-container="body"
                                        data-toggle="popover" data-placement="bottom"
                                        data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                    Popover on bottom
                                </button>
                            </div>
                            <div class="col-md-3 m-t-b-20">
                                <button type="button" class="btn btn-secondary" data-container="body"
                                        data-toggle="popover" data-placement="left"
                                        data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus.">
                                    Popover on left
                                </button>
                            </div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover" data-placement="top" data-content="Vivamus sagittis lacus vel augue laoreet rutrum faucibus."&gt;
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

            </div>
        </div>

    </div>

@endsection


@section('script')
    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
