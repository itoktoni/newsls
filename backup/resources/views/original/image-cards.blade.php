@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Image Cards</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Image Cards</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="card-group">
                    <div class="card">
                        <img src="{{ url('assets/media/image/portfolio-six.jpg') }}" class="card-img-top" alt="image">
                        <div class="card-body">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This is a wider card with supporting text below as a natural
                                lead-in to
                                additional content. This content is a little bit longer.</p>
                            <p class="card-text">
                                <small class="text-muted">Last updated 3 mins ago</small>
                            </p>
                        </div>
                    </div>
                    <div class="card">
                        <img src="{{ url('assets/media/image/portfolio-four.jpg') }}" class="card-img-top" alt="image">
                        <div class="card-body">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This card has supporting text below as a natural lead-in to
                                additional
                                content.</p>
                            <p class="card-text">
                                <small class="text-muted">Last updated 3 mins ago</small>
                            </p>
                        </div>
                    </div>
                    <div class="card">
                        <img src="{{ url('assets/media/image/portfolio-two.jpg') }}" class="card-img-top" alt="image">
                        <div class="card-body">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This is a wider card with supporting text below as a natural
                                lead-in to
                                additional content. This card has even longer content than the first to show that
                                equal height
                                action.</p>
                            <p class="card-text">
                                <small class="text-muted">Last updated 3 mins ago</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-columns">

                    <div class="card">
                        <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" class="card-img-top" alt="image">
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build on the card title and make up the
                                bulk of the
                                card's content.</p>
                        </div>
                    </div>

                    <div class="card">
                        <img src="{{ url('assets/media/image/portfolio-two.jpg') }}" class="card-img-top" alt="image">
                        <div class="card-body">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This is a wider card with supporting text below as a natural
                                lead-in to
                                additional content. This content is a little bit longer.</p>
                            <p class="card-text">
                                <small class="text-muted">Last updated 3 mins ago</small>
                            </p>
                        </div>
                    </div>

                    <div class="card">
                        <img src="{{ url('assets/media/image/portfolio-four.jpg') }}" class="card-img-top" alt="image">
                        <div class="card-body">
                            <p class="card-text">Some quick example text to build on the card title and make up the
                                bulk of the
                                card's content.</p>
                            <a href="#" class="btn btn-primary">Read More</a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This is a wider card with supporting text below as a natural
                                lead-in to
                                additional content. This content is a little bit longer.</p>
                            <p class="card-text">
                                <small class="text-muted">Last updated 3 mins ago</small>
                            </p>
                        </div>
                        <img src="{{ url('assets/media/image/portfolio-three.jpg') }}" class="card-img-top" alt="image">
                    </div>

                    <div class="card bg-dark text-white">
                        <img src="{{ url('assets/media/image/portfolio-six.jpg') }}" class="card-img" alt="image">
                        <div class="card-img-overlay">
                            <h6 class="card-title">Card title</h6>
                            <p class="card-text">This is a wider card with supporting text below as a natural
                                lead-in to
                                additional content. This content is a little bit longer.</p>
                            <p class="card-text">Last updated 3 mins ago</p>
                        </div>
                    </div>

                    <div class="card">
                        <img src="{{ url('assets/media/image/portfolio-five.jpg') }}" class="card-img-top" alt="image">
                    </div>

                    <div class="card">
                        <div class="card-header">Card Header</div>
                        <div class="card-body">
                            <figure>
                                <img src="{{ url('assets/media/image/portfolio-four.jpg') }}" class="card-img-top" alt="image">
                            </figure>
                            <p class="card-text">Some quick example text to build on the card title and make up the
                                bulk of the
                                card's content.</p>
                            <a href="#" class="btn btn-primary">Read More</a>
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
