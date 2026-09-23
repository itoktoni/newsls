@extends('layouts.app')

@section('head')
    <!-- Slick slide -->
    <link rel="stylesheet" href="{{ url('vendors/slick/slick.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ url('vendors/slick/slick-theme.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Slick Slide</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Plugins</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Slick Slide</li>
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
                        <p class="lead">Content sliders can provide an engaging visual way to showcase your
                            website's unique content. Here's how to use Slick Slider to make a creative content
                            slider. Click <a href="https://kenwheeler.github.io/slick/" class="link-1"
                                             target="_blank">here</a> for more features.</p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Style --&gt;
&lt;link rel="stylesheet" href="vendors/slick/slick.css" type="text/css"&gt;
&lt;link rel="stylesheet" href="vendors/slick/slick-theme.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/slick/slick.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="your-class"&gt;
  &lt;div&gt;your content&lt;/div&gt;
  &lt;div&gt;your content&lt;/div&gt;
  &lt;div&gt;your content&lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$(document).ready(function(){
  $('.your-class').slick({
    setting-name: setting-value
  });
});</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Single Item</h6>
                        <div class="row">
                            <div class="col-md-6 offset-3">
                                <div class="slick-single">
                                    <div>
                                        <img src="{{ url('assets/media/image/photo1.jpg') }}" style="width: 100%"
                                             alt="image">
                                    </div>
                                    <div>
                                        <img src="{{ url('assets/media/image/photo2.jpg') }}" style="width: 100%"
                                             alt="image">
                                    </div>
                                    <div>
                                        <img src="{{ url('assets/media/image/photo3.jpg') }}" style="width: 100%"
                                             alt="image">
                                    </div>
                                    <div>
                                        <img src="{{ url('assets/media/image/photo4.jpg') }}" style="width: 100%"
                                             alt="image">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-js">$('.slick-single').slick();</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Autoplay</h6>
                        <div class="slick-autoplay">
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo1.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo2.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo3.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo4.jpg') }}" class="img-fluid" alt="image">
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">$('.slick-autoplay').slick({
    slidesToShow: 3,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
});</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Fade Effect</h6>
                        <div class="row">
                            <div class="col-md-6 offset-3">
                                <div class="slick-fade-effect">
                                    <div>
                                        <img src="{{ url('assets/media/image/photo1.jpg') }}" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <img src="{{ url('assets/media/image/photo2.jpg') }}" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <img src="{{ url('assets/media/image/photo3.jpg') }}" class="img-fluid" alt="image">
                                    </div>
                                    <div>
                                        <img src="{{ url('assets/media/image/photo4.jpg') }}" class="img-fluid" alt="image">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">$('.slick-fade-effect').slick({
    dots: true,
    infinite: true,
    speed: 500,
    fade: true,
    cssEase: 'linear'
});</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Multiple Items</h6>
                        <div class="slick-multiple">
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo1.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo2.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo3.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo4.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo5.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo6.jpg') }}" class="img-fluid" alt="image">
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">$('.slick-multiple').slick({
    infinite: true,
    slidesToShow: 4,
    slidesToScroll: 4
});</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Center Mode</h6>
                        <div class="slick-center-mode">
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo1.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo2.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo3.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo4.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo5.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo6.jpg') }}" class="img-fluid" alt="image">
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">$('.slick-center-mode').slick({
        centerMode: true,
        centerPadding: '60px',
        slidesToShow: 3,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    centerMode: true,
                    centerPadding: '40px',
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 480,
                settings: {
                    arrows: false,
                    centerMode: true,
                    centerPadding: '40px',
                    slidesToShow: 1
                }
            }
        ]
    });</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Slider Syncing</h6>
                        <div class="slider-for">
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo1.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo2.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo3.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo4.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo5.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo6.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo7.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo8.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo9.jpg') }}" class="img-fluid" style="width:100%"
                                     alt="image">
                            </div>
                        </div>
                        <div class="slider-nav">
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo1.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo2.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo3.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo4.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo5.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo6.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo7.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo8.jpg') }}" class="img-fluid" alt="image">
                            </div>
                            <div class="slick-slide-item">
                                <img src="{{ url('assets/media/image/photo9.jpg') }}" class="img-fluid" alt="image">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="slider-for"&gt;
  slick items...
&lt;/div&gt;
&lt;div class="slider-nav"&gt;
  slick items...
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('.slider-for').slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: false,
    fade: true,
    asNavFor: '.slider-nav'
});

$('.slider-nav').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    asNavFor: '.slider-for',
    centerMode: true,
    focusOnSelect: true
});</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6"></div>
            <div class="col-md-6"></div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Slick slide -->
    <script src="{{ url('vendors/slick/slick.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/slick.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
