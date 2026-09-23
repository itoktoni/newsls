@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Other Cards</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Other Cards</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h5 class="card-title">Primary card title</h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h6 class="card-title">Secondary card title</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h6 class="card-title">Success card title</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h6 class="card-title">Danger card title</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h6 class="card-title">Warning card title</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h6 class="card-title">Info card title</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-light">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h6 class="card-title">Light card title</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-dark">
                    <div class="card-header">Header</div>
                    <div class="card-body">
                        <h6 class="card-title">Dark card title</h6>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk
                            of
                            the card's content.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h5>Card Columns</h5>
                <hr>
                <div class="card-columns">
                    <div class="card p-3">
                        <blockquote class="blockquote mb-0 card-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a
                                ante.</p>
                            <footer class="blockquote-footer">
                                <small class="text-muted">
                                    Someone famous in <cite title="Source Title">Source Title</cite>
                                </small>
                            </footer>
                        </blockquote>
                    </div>
                    <div class="card bg-primary text-center p-3">
                        <blockquote class="blockquote mb-0 card-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a
                                ante.</p>
                            <footer class="blockquote-footer">
                                <small class="text-white opacity-6">
                                    Someone famous in <cite title="Source Title">Source Title</cite>
                                </small>
                            </footer>
                        </blockquote>
                    </div>
                    <div class="card bg-success text-center p-3">
                        <blockquote class="blockquote mb-0 card-body">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a
                                ante.</p>
                            <footer class="blockquote-footer">
                                <small class="text-white opacity-6">
                                    Someone famous in <cite title="Source Title">Source Title</cite>
                                </small>
                            </footer>
                        </blockquote>
                    </div>
                    <div class="card text-center">
                        <div class="card-body">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This card has a regular title and short paragraphy of text below
                                it.</p>
                            <p class="card-text">
                                <small class="text-muted">Last updated 3 mins ago</small>
                            </p>
                        </div>
                    </div>
                    <div class="card p-3 text-right">
                        <blockquote class="blockquote card-body mb-0">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a
                                ante.</p>
                            <footer class="blockquote-footer">
                                <small class="text-muted">
                                    Someone famous in <cite title="Source Title">Source Title</cite>
                                </small>
                            </footer>
                        </blockquote>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This is another card with title and supporting text below. This
                                card
                                has some additional content to make it slightly taller overall.</p>
                            <p class="card-text">
                                <small class="text-muted">Last updated 3 mins ago</small>
                            </p>
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
