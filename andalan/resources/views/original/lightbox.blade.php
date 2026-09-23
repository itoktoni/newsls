@extends('layouts.app')

@section('head')
    <!-- Lightbox -->
    <link rel="stylesheet" href="{{ url('vendors/lightbox/magnific-popup.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Lightbox</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Plugins</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Lightbox</li>
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
                        <h6 class="card-title">Basic</h6>
                        <div class="row">
                            <div class="col-md-2">
                                <a class="image-popup" href="{{ url('assets/media/image/photo1.jpg') }}">
                                    <img src="{{ url('assets/media/image/photo1.jpg') }}" class="img-fluid" alt="image">
                                </a>
                            </div>
                        </div>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Css --&gt;
&lt;link rel="stylesheet" href="vendors/lightbox/magnific-popup.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/lightbox/jquery.magnific-popup.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;a class="image-popup" href="big-image.jpg"&gt;
  &lt;img src="small-image.jpg" alt="image"&gt;
&lt;/a&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('.image-popup').magnificPopup({
    type: 'image',
    zoom: {
        enabled: true,
        duration: 300,
        easing: 'ease-in-out',
        opener: function(openerElement) {
            return openerElement.is('img') ? openerElement : openerElement.find('img');
        }
    }
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Gallery Version</h6>
                        <div class="row">
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                                <a class="image-popup-gallery-item" href="{{ url('assets/media/image/photo4.jpg') }}">
                                    <img src="{{ url('assets/media/image/photo4.jpg') }}" class="img-fluid" alt="image">
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                                <a class="image-popup-gallery-item" href="{{ url('assets/media/image/photo5.jpg') }}">
                                    <img src="{{ url('assets/media/image/photo5.jpg') }}" class="img-fluid" alt="image">
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                                <a class="image-popup-gallery-item" href="{{ url('assets/media/image/photo6.jpg') }}">
                                    <img src="{{ url('assets/media/image/photo6.jpg') }}" class="img-fluid" alt="image">
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                                <a class="image-popup-gallery-item" href="{{ url('assets/media/image/photo7.jpg') }}">
                                    <img src="{{ url('assets/media/image/photo7.jpg') }}" class="img-fluid" alt="image">
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                                <a class="image-popup-gallery-item" href="{{ url('assets/media/image/photo8.jpg') }}">
                                    <img src="{{ url('assets/media/image/photo8.jpg') }}" class="img-fluid" alt="image">
                                </a>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
                                <a class="image-popup-gallery-item" href="{{ url('assets/media/image/photo9.jpg') }}">
                                    <img src="{{ url('assets/media/image/photo9.jpg') }}" class="img-fluid" alt="image">
                                </a>
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="row"&gt;
    &lt;div class="col-md-2 m-b-15"&gt;
        &lt;a class="image-popup-gallery-item" href="big-image.jpg"&gt;
            &lt;img src="small-image.jpg" class="img-fluid" alt="image"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
    &lt;div class="col-md-2 m-b-15"&gt;
        &lt;a class="image-popup-gallery-item" href="big-image.jpg"&gt;
            &lt;img src="small-image.jpg" class="img-fluid" alt="image"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
    &lt;div class="col-md-2 m-b-15"&gt;
        &lt;a class="image-popup-gallery-item" href="big-image.jpg"&gt;
            &lt;img src="small-image.jpg" class="img-fluid" alt="image"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
    &lt;div class="col-md-2 m-b-15"&gt;
        &lt;a class="image-popup-gallery-item" href="big-image.jpg"&gt;
            &lt;img src="small-image.jpg" class="img-fluid" alt="image"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
    &lt;div class="col-md-2 m-b-15"&gt;
        &lt;a class="image-popup-gallery-item" href="big-image.jpg"&gt;
            &lt;img src="small-image.jpg" class="img-fluid" alt="image"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
    &lt;div class="col-md-2 m-b-15"&gt;
        &lt;a class="image-popup-gallery-item" href="big-image.jpg"&gt;
            &lt;img src="small-image.jpg" class="img-fluid" alt="image"&gt;
        &lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">let magnificPopupGalleryConfig = {
    type: 'image',
    gallery: {
        enabled: true
    },
    zoom: {
        enabled: true,
        duration: 300,
        easing: 'ease-in-out',
        opener: function(openerElement) {
            return openerElement.is('img') ? openerElement : openerElement.find('img');
        }
    }
};

$('.image-popup-gallery-item').magnificPopup(magnificPopupGalleryConfig);</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Lightbox -->
    <script src="{{ url('vendors/lightbox/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/lightbox.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
