@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Icons</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Icons</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Feather Icons</h6>
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="anchor"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="archive"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="calendar"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="clock"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="coffee"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="database"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="droplet"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="folder"></i>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="globe"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="layout"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="layers"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="image"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="upload"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="plus"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="home"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i data-feather="frown"></i>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-html">&lt;i data-feather="anchor"&gt;&lt;/i&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">FontAwesome</h6>
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-info-circle"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-user-o"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-users"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-smile-o"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-truck"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-file"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-yoast"></i>
                                </div>
                            </div>

                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-window-minimize"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-download"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-xing"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-warning"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-upload"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-database"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-plus"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="fa fa-vimeo"></i>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code
                                        class="language-html">&lt;i class="fa fa-info-circle"&gt;&lt;/i&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Themify Icons</h6>
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-hand-open"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-facebook"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-menu"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-agenda"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-face-sad"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-check"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-close"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-angle-left"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-bell"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-blackboard"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-apple"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-alert"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-book"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-gallery"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-key"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="ti-settings"></i>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-html">&lt;i class="ti-gallery"&gt;&lt;/i&gt;</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Weather Icons</h6>
                        <div class="row">
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-thermometer-1-f"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-cloud-down"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-drop"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-eclipse-1"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-fog-1"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-hail-1"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-heavy-hail-night"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-moon-1"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-partly-cloudy-1"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-umbrella"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-wind-2"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-moon-last-quarter-f"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-sun-horizon-1-f"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-drizzle-f"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-partly-cloudy-1-f"></i>
                                </div>
                            </div>
                            <div class="col-lg-3 col-6">
                                <div class="demo-icon-list">
                                    <i class="pe-is-w-wind-cone-f"></i>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code
                                        class="language-html">&lt;i class="pe-is-w-umbrella"&gt;&lt;/i&gt;</code></pre>
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
