@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Progress</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Progress</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Progress</h6>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: 50%" aria-valuenow="50"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: 75%" aria-valuenow="75"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 100%" aria-valuenow="100"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                    <pre><code class="language-html">&lt;div class="progress-bar" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Labels</h6>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100">25%
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="progress"&gt;
  &lt;div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"&gt;
     25%
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Height</h6>
                <div class="progress mb-3" style="height: 3px;">
                    <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress" style="height: 25px;">
                    <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="progress" style="height: 3px;"&gt;
  &lt;div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;

&lt;div class="progress"&gt;
  &lt;div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;

&lt;div class="progress" style="height: 25px;"&gt;
  &lt;div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Backgrounds</h6>
                <div class="progress mb-3">
                    <div class="progress-bar bg-success" role="progressbar" style="width: 25%"
                         aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-info" role="progressbar" style="width: 50%"
                         aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-warning" role="progressbar" style="width: 75%"
                         aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: 100%"
                         aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="progress"&gt;
  &lt;div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;

&lt;div class="progress"&gt;
  &lt;div class="progress-bar bg-info" role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;

&lt;div class="progress"&gt;
  &lt;div class="progress-bar bg-warning" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;

&lt;div class="progress"&gt;
  &lt;div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Multiple bars</h6>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: 15%" aria-valuenow="15"
                         aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="30"
                         aria-valuemin="0" aria-valuemax="100"></div>
                    <div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20"
                         aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="progress"&gt;
  &lt;div class="progress-bar" role="progressbar" style="width: 15%" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
  &lt;div class="progress-bar bg-success" role="progressbar" style="width: 30%" aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
  &lt;div class="progress-bar bg-info" role="progressbar" style="width: 20%" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Striped</h6>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%"
                         aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped bg-success" role="progressbar"
                         style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped bg-info" role="progressbar"
                         style="width: 50%" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped bg-warning" role="progressbar"
                         style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped bg-danger" role="progressbar"
                         style="width: 100%" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="progress"&gt;
  &lt;div class="progress-bar progress-bar-striped" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Animated stripes</h6>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                         style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                         role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                         role="progressbar" style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>
                <div class="progress mb-3">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-warning"
                         role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger"
                         role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0"
                         aria-valuemax="100"></div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="progress"&gt;
  &lt;div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 10%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"&gt;&lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('script')
    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
