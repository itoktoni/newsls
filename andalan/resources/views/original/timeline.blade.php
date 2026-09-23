@extends('layouts.app')

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Timeline</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Pages</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Timeline</li>
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
                        <div class="timeline">
                            <div class="timeline-item">
                                <div>
                                    <a href="#">
                                        <figure class="avatar avatar-sm mr-3 bring-forward">
                                            <img src="{{ url('assets/media/image/user/women_avatar4.jpg') }}"
                                                 class="rounded-circle" alt="image">
                                        </figure>
                                    </a>
                                </div>
                                <div>
                                    <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Martina Ash</a> shared a link
                                            </span>
                                        <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                    </h6>
                                    <a href="#">
                                        <div class="mb-3 border border-radius-1">
                                            <div class="row no-gutters">
                                                <div class="col-1">
                                                    <img src="{{ url('assets/media/image/photo6.jpg') }}" class="w-100"
                                                         alt="image">
                                                </div>
                                                <div class="col-10 p-3">
                                                    <h5 class="line-height-16">Connection title</h5>
                                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                                                    Facilis, voluptatibus.
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div>
                                    <figure class="avatar avatar-sm mr-3 bring-forward">
                                        <span class="avatar-title bg-success-bright text-success rounded-circle">C</span>
                                    </figure>
                                </div>
                                <div>
                                    <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Jonny Richie</a> shared a post
                                            </span>
                                        <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                    </h6>
                                    <a href="#">
                                        <div class="mb-3 border p-3 border-radius-1">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquid
                                            aperiam commodi culpa debitis deserunt enim itaque laborum minima neque
                                            nostrum pariatur perspiciatis, placeat quidem, ratione recusandae
                                            reiciendis sapiente, ut veritatis vitae. Beatae dolore hic odio! Esse
                                            officiis quidem voluptate.
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div>
                                    <figure class="avatar avatar-sm mr-3 bring-forward">
                                        <span class="avatar-title bg-info-bright text-info rounded-circle">K</span>
                                    </figure>
                                </div>
                                <div>
                                    <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Jonny Richie</a> attached file
                                            </span>
                                        <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                    </h6>
                                    <a href="#">
                                        <div class="mb-3 border p-3 border-radius-1">
                                            <i class="fa fa-file-pdf-o mr-2"></i> filename12334.pdf
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div>
                                    <figure class="avatar avatar-sm mr-3 bring-forward">
                                            <span class="avatar-title bg-warning-bright text-warning rounded-circle">
                                                <i class="ti-image"></i>
                                            </span>
                                    </figure>
                                </div>
                                <div>
                                    <h6 class="d-flex justify-content-between mb-4">
                                            <span>
                                                <a href="#">Jonny Richie</a> shared files
                                            </span>
                                        <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                    </h6>
                                    <div class="row row-xs">
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sx-6">
                                            <figure>
                                                <img src="{{ url('assets/media/image/portfolio-five.jpg') }}"
                                                     class="w-100 border-radius-1" alt="image">
                                            </figure>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sx-6">
                                            <figure>
                                                <img src="{{ url('assets/media/image/portfolio-one.jpg') }}"
                                                     class="w-100 border-radius-1" alt="image">
                                            </figure>
                                        </div>
                                        <div class="col-xl-2 col-lg-3 col-md-4 col-sx-6">
                                            <figure>
                                                <img src="{{ url('assets/media/image/portfolio-three.jpg') }}"
                                                     class="w-100 border-radius-1" alt="image">
                                            </figure>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div>
                                    <figure class="avatar avatar-sm mr-3 bring-forward">
                                        <span class="avatar-title bg-youtube rounded-circle">Y</span>
                                    </figure>
                                </div>
                                <div>
                                    <h6 class="d-flex justify-content-between">
                                           <span>
                                               <a href="#">Jonny Richie</a> shared video
                                           </span>
                                        <span class="text-muted font-weight-normal">Tue 8:17pm</span>
                                    </h6>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Iusto, placeat.</p>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="embed-responsive embed-responsive-16by9 m-t-b-5">
                                                <iframe class="embed-responsive-item"
                                                        src="https://www.youtube.com/embed/l-epKcOA7RQ"
                                                        allowfullscreen></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
