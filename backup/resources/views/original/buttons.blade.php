@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Buttons</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Buttons</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Default</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-primary">Primary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-secondary">Secondary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success">Success</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger">Danger</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-warning">Warning</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-info">Info</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-light">Light</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-dark">Dark</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary"&gt;Primary&lt;/button&gt;
&lt;button type="button" class="btn btn-secondary"&gt;Secondary&lt;/button&gt;
&lt;button type="button" class="btn btn-success"&gt;Success&lt;/button&gt;
&lt;button type="button" class="btn btn-danger"&gt;Danger&lt;/button&gt;
&lt;button type="button" class="btn btn-warning"&gt;Warning&lt;/button&gt;
&lt;button type="button" class="btn btn-info"&gt;Info&lt;/button&gt;
&lt;button type="button" class="btn btn-light"&gt;Light&lt;/button&gt;
&lt;button type="button" class="btn btn-dark"&gt;Dark&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Square</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-primary btn-square">Primary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-square">Secondary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success btn-square">Success</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger btn-square">Danger</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-warning btn-square">Warning</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-info btn-square">Info</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-light btn-square">Light</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-dark btn-square">Dark</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                    <pre><code class="language-html">&lt;button type="button" class="btn btn-primary btn-square"&gt;Primary&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Outline</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-primary">Primary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary">Secondary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-success">Success</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-danger">Danger</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-warning">Warning</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-info">Info</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-light">Light</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-dark">Dark</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                    <pre><code class="language-html">&lt;button type="button" class="btn btn-outline-primary"&gt;Primary&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Rounded</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-primary btn-rounded">Primary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-rounded">Secondary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success btn-rounded">Success</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger btn-rounded">Danger</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-warning btn-rounded">Warning</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-info btn-rounded">Info</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-light btn-rounded">Light</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-dark btn-rounded">Dark</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                    <pre><code class="language-html">&lt;button type="button" class="btn btn-primary btn-rounded"&gt;Primary&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Uppercase</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-primary btn-uppercase">Primary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-uppercase">Secondary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success btn-uppercase">Success</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger btn-uppercase">Danger</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-warning btn-uppercase">Warning</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-info btn-uppercase">Info</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-light btn-uppercase">Light</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-dark btn-uppercase">Dark</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                    <pre><code class="language-html">&lt;button type="button" class="btn btn-primary btn-uppercase"&gt;Primary&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Disabled</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-primary disabled">Primary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-secondary disabled">Secondary</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success disabled">Success</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger disabled">Danger</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-warning disabled">Warning</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-info disabled">Info</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-light disabled">Light</button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-dark disabled">Dark</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary disabled"&gt;Primary&lt;/button&gt;

&lt;!-- or --&gt;

&lt;button type="button" class="btn btn-primary" disabled&gt;Primary&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Button with Icon</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary btn-uppercase">
                            <i class="ti-plus mr-2"></i> Create User
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success btn-uppercase">
                            <i class="ti-check-box mr-2"></i> Save
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-warning btn-rounded btn-uppercase">
                            <i class="ti-settings mr-2"></i> Settings
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger btn-square btn-uppercase">
                            <i class="ti-trash mr-2"></i> Delete
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-outline-secondary btn-uppercase"&gt;
  &lt;i class="ti-plus mr-2"&gt;&lt;/i&gt; Create User
&lt;/button&gt;
&lt;button type="button" class="btn btn-success btn-uppercase"&gt;
  &lt;i class="ti-check-box mr-2"&gt;&lt;/i&gt; Save
&lt;/button&gt;
&lt;button type="button" class="btn btn-outline-warning btn-rounded btn-uppercase"&gt;
  &lt;i class="ti-settings mr-2"&gt;&lt;/i&gt; Settings
&lt;/button&gt;
&lt;button type="button" class="btn btn-danger btn-square btn-uppercase"&gt;
  &lt;i class="ti-trash mr-2"&gt;&lt;/i&gt; Delete
&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Only Icon</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary">
                            <i class="ti-plus"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success">
                            <i class="ti-check-box"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-warning btn-rounded">
                            <i class="ti-settings"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger btn-square">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-outline-secondary"&gt;
  &lt;i class="ti-plus"&gt;&lt;/i&gt;
&lt;/button&gt;
&lt;button type="button" class="btn btn-success"&gt;
  &lt;i class="ti-check-box"&gt;&lt;/i&gt;
&lt;/button&gt;
&lt;button type="button" class="btn btn-warning btn-rounded"&gt;
  &lt;i class="ti-settings"&gt;&lt;/i&gt;
&lt;/button&gt;
&lt;button type="button" class="btn btn-danger btn-square"&gt;
  &lt;i class="ti-trash"&gt;&lt;/i&gt;
&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Only Icon Floating</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-floating">
                            <i class="ti-plus"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-success btn-floating">
                            <i class="ti-check-box"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-warning btn-floating">
                            <i class="ti-settings"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-danger btn-floating">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary btn-floating">
                            <i class="ti-plus"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-success btn-floating">
                            <i class="ti-check-box"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-warning btn-floating">
                            <i class="ti-settings"></i>
                        </button>
                    </div>
                    <div class="col-md-3 m-t-b-20">
                        <button type="button" class="btn btn-outline-danger btn-floating">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-secondary btn-floating"&gt;
  &lt;i class="ti-plus"&gt;&lt;/i&gt;
&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Loader Buttons</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button class="btn btn-primary" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm mr-2" role="status"
                                          aria-hidden="true"></span>
                            Loading...
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button class="btn btn-primary" type="button" disabled>
                                    <span class="spinner-border spinner-border-sm" role="status"
                                          aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button class="btn btn-primary btn-floating" type="button" disabled>
                            <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button class="btn btn-outline-primary" type="button" disabled>
                                    <span class="spinner-grow spinner-grow-sm mr-2" role="status"
                                          aria-hidden="true"></span>
                            Loading...
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary" disabled&gt;
  &lt;span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"&gt;&lt;/span&gt;
  Loading...
&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Pulse</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-primary btn-pulse">
                            <i class="ti-user mr-2"></i> Profile
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-warning btn-pulse">
                            <i class="ti-settings mr-2"></i> Settings
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-pulse">
                            <i class="ti-plus"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-success btn-floating btn-pulse">
                            <i class="ti-check"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-danger btn-lg btn-floating btn-pulse">
                            <i class="ti-trash"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary btn-lg btn-floating btn-pulse">
                            <i class="ti-plus"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-whatsapp btn-floating btn-lg btn-pulse">
                            <i class="fa fa-whatsapp"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-dribbble btn-floating btn-lg btn-pulse">
                            <i class="fa fa-dribbble"></i>
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary btn-pulse"&gt;
  &lt;i class="ti-user mr-2"&gt;&lt;/i&gt; Profile
&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Store Buttons</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-lg-5 col-sm-6 m-t-b-20">
                        <a href="#" class="btn btn-apple">
                            <i class="fa fa-apple"></i>
                            <span>
                                   <span>Download on the</span>
                                   <span>App Store</span>
                              </span>
                        </a>
                    </div>
                    <div class="col-lg-5 col-sm-6 m-t-b-20">
                        <a href="#" class="btn btn-google-play">
                            <img src="{{ url('assets/media/image/google_play_button.png') }}" alt="image">
                            <span>
                            <span>Get in on</span>
                            <span>Google Play</span>
                        </span>
                        </a>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;a href="#" class="btn btn-apple"&gt;
  &lt;i class="fa fa-apple"&gt;&lt;/i&gt;
  &lt;span&gt;
    &lt;span&gt;Download on the&lt;/span&gt;
    &lt;span&gt;App Store&lt;/span&gt;
  &lt;/span&gt;
&lt;/a&gt;

&lt;a href="#" class="btn btn-google-play"&gt;
  &lt;img src="google_play_button.png" alt="image"&gt;
  &lt;span&gt;
     &lt;span&gt;Get in on&lt;/span&gt;
     &lt;span&gt;Google Play&lt;/span&gt;
   &lt;/span&gt;
&lt;/a&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Social Buttons</h6>
                <div class="row text-center justify-content-md-center">
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-facebook">
                            <i class="fa fa-facebook-square mr-2"></i> Facebook
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-google">
                            <i class="fa fa-google mr-2"></i> Google
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-twitter">
                            <i class="fa fa-twitter mr-2"></i> Twitter
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-linkedin">
                            <i class="fa fa-linkedin mr-2"></i> Linkedin
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-facebook">
                            <i class="fa fa-facebook-square mr-2"></i> Facebook
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-google">
                            <i class="fa fa-google mr-2"></i> Google
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-twitter">
                            <i class="fa fa-twitter mr-2"></i> Twitter
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-linkedin">
                            <i class="fa fa-linkedin mr-2"></i> Linkedin
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-facebook">
                            <i class="fa fa-facebook-square"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-google">
                            <i class="fa fa-google"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-twitter">
                            <i class="fa fa-twitter"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-linkedin">
                            <i class="fa fa-linkedin"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-facebook btn-floating">
                            <i class="fa fa-facebook-square"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-google btn-floating">
                            <i class="fa fa-google"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-twitter btn-floating">
                            <i class="fa fa-twitter"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-linkedin btn-floating">
                            <i class="fa fa-linkedin"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-whatsapp">
                            <i class="fa fa-whatsapp mr-2"></i> Whatsapp
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-instagram">
                            <i class="fa fa-instagram mr-2"></i> Instagram
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-dribbble">
                            <i class="fa fa-dribbble mr-2"></i> Dribbble
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-youtube">
                            <i class="fa fa-youtube mr-2"></i> Youtube
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-whatsapp">
                            <i class="fa fa-whatsapp mr-2"></i> Whatsapp
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-instagram">
                            <i class="fa fa-instagram mr-2"></i> Instagram
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-dribbble">
                            <i class="fa fa-dribbble mr-2"></i> Dribbble
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-youtube">
                            <i class="fa fa-youtube mr-2"></i> Youtube
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 mb-5">
                        <button type="button" class="btn btn-whatsapp">
                            <i class="fa fa-whatsapp"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-instagram">
                            <i class="fa fa-instagram"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-dribbble">
                            <i class="fa fa-dribbble"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-youtube">
                            <i class="fa fa-youtube"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-whatsapp btn-floating">
                            <i class="fa fa-whatsapp"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-instagram btn-floating">
                            <i class="fa fa-instagram"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-dribbble btn-floating">
                            <i class="fa fa-dribbble"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-youtube btn-floating">
                            <i class="fa fa-youtube"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-github">
                            <i class="fa fa-github mr-2"></i> Github
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-behance">
                            <i class="fa fa-behance mr-2"></i> Behance
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-skype">
                            <i class="fa fa-skype mr-2"></i> Skype
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-yahoo">
                            <i class="fa fa-yahoo mr-2"></i> Yahoo
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-github">
                            <i class="fa fa-github mr-2"></i> Github
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-behance">
                            <i class="fa fa-behance mr-2"></i> Behance
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-skype">
                            <i class="fa fa-skype mr-2"></i> Skype
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-outline-yahoo">
                            <i class="fa fa-yahoo mr-2"></i> Yahoo
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-github">
                            <i class="fa fa-github"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-behance">
                            <i class="fa fa-behance"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-skype">
                            <i class="fa fa-skype"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-yahoo">
                            <i class="fa fa-yahoo"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-github btn-floating">
                            <i class="fa fa-github"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-behance btn-floating">
                            <i class="fa fa-behance"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-skype btn-floating">
                            <i class="fa fa-skype"></i>
                        </button>
                    </div>
                    <div class="col-lg-3 col-sm-6 m-t-b-20">
                        <button type="button" class="btn btn-yahoo btn-floating">
                            <i class="fa fa-yahoo"></i>
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-facebook"&gt;
  &lt;i class="fa fa-facebook-square mr-2"&gt;&lt;/i&gt; Facebook
&lt;/button&gt;
&lt;button type="button" class="btn btn-google"&gt;
  &lt;i class="fa fa-google mr-2"&gt;&lt;/i&gt; Google
&lt;/button&gt;
&lt;button type="button" class="btn btn-twitter"&gt;
  &lt;i class="fa fa-twitter mr-2"&gt;&lt;/i&gt; Twitter
&lt;/button&gt;
&lt;button type="button" class="btn btn-linkedin"&gt;
  &lt;i class="fa fa-linkedin mr-2"&gt;&lt;/i&gt; Linkedin
&lt;/button&gt;
...</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Sizes</h6>
                <div class="row text-center justify-content-md-center align-items-center">

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-sm">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-lg">Button</button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-square btn-sm">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-square">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-square btn-lg">Button</button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary btn-sm">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-outline-secondary btn-lg">Button</button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-rounded btn-sm">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-rounded">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-rounded btn-lg">Button</button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-uppercase btn-sm">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-uppercase">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-uppercase btn-lg">Button</button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary disabled btn-sm">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary disabled">Button</button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary disabled btn-lg">Button</button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-sm">
                            <i class="ti-settings m-r-5"></i> Button
                        </button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary">
                            <i class="ti-settings m-r-5"></i> Button
                        </button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-lg">
                            <i class="ti-settings m-r-10"></i> Button
                        </button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-sm">
                            <i class="ti-control-play"></i>
                        </button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary">
                            <i class="ti-control-play"></i>
                        </button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-lg">
                            <i class="ti-control-play"></i>
                        </button>
                    </div>

                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-floating btn-sm">
                            <i class="ti-control-play"></i>
                        </button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-floating">
                            <i class="ti-control-play"></i>
                        </button>
                    </div>
                    <div class="col-md-4 m-t-b-20">
                        <button type="button" class="btn btn-secondary btn-floating btn-lg">
                            <i class="ti-control-play"></i>
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-secondary btn-sm"&gt;Button&lt;/button&gt;
&lt;button type="button" class="btn btn-secondary"&gt;Button&lt;/button&gt;
&lt;button type="button" class="btn btn-secondary btn-lg"&gt;Button&lt;/button&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Button Group</h6>

                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-dark">Left</button>
                    <button type="button" class="btn btn-dark">Middle</button>
                    <button type="button" class="btn btn-dark">Right</button>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="btn-group" role="group" aria-label="Basic example"&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Left&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Middle&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Right&lt;/button&gt;
&lt;/div&gt;</code></pre>
                </div>

                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-primary">
                        <i class="ti-control-pause"></i>
                    </button>
                    <button type="button" class="btn btn-success">
                        <i class="ti-control-play"></i>
                    </button>
                    <button type="button" class="btn btn-warning">
                        <i class="ti-control-forward"></i>
                    </button>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="btn-group" role="group" aria-label="Basic example"&gt;
  &lt;button type="button" class="btn btn-primary"&gt;
    &lt;i class="ti-control-pause"&gt;&lt;/i&gt;
  &lt;/button&gt;
  &lt;button type="button" class="btn btn-success"&gt;
    &lt;i class="ti-control-play"&gt;&lt;/i&gt;
  &lt;/button&gt;
  &lt;button type="button" class="btn btn-warning"&gt;
    &lt;i class="ti-control-forward"&gt;&lt;/i&gt;
  &lt;/button&gt;
&lt;/div&gt;</code></pre>
                </div>

                <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="btn-group mr-2" role="group" aria-label="First group">
                        <button type="button" class="btn btn-dark">1</button>
                        <button type="button" class="btn btn-dark">2</button>
                        <button type="button" class="btn btn-dark">3</button>
                        <button type="button" class="btn btn-dark">4</button>
                    </div>
                    <div class="btn-group mr-2" role="group" aria-label="Second group">
                        <button type="button" class="btn btn-dark">5</button>
                        <button type="button" class="btn btn-dark">6</button>
                        <button type="button" class="btn btn-dark">7</button>
                    </div>
                    <div class="btn-group" role="group" aria-label="Third group">
                        <button type="button" class="btn btn-dark">8</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups"&gt;
  &lt;div class="btn-group mr-2" role="group" aria-label="First group"&gt;
    &lt;button type="button" class="btn btn-dark"&gt;1&lt;/button&gt;
    &lt;button type="button" class="btn btn-dark"&gt;2&lt;/button&gt;
    &lt;button type="button" class="btn btn-dark"&gt;3&lt;/button&gt;
    &lt;button type="button" class="btn btn-dark"&gt;4&lt;/button&gt;
  &lt;/div&gt;
  &lt;div class="btn-group mr-2" role="group" aria-label="Second group"&gt;
    &lt;button type="button" class="btn btn-dark"&gt;5&lt;/button&gt;
    &lt;button type="button" class="btn btn-dark"&gt;6&lt;/button&gt;
    &lt;button type="button" class="btn btn-dark"&gt;7&lt;/button&gt;
  &lt;/div&gt;
  &lt;div class="btn-group" role="group" aria-label="Third group"&gt;
    &lt;button type="button" class="btn btn-dark"&gt;8&lt;/button&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>

                <div>
                    <div class="btn-group btn-group-lg mb-3" role="group" aria-label="Large button group">
                        <button type="button" class="btn btn-dark">Left</button>
                        <button type="button" class="btn btn-dark">Middle</button>
                        <button type="button" class="btn btn-dark">Right</button>
                    </div>
                    <br>
                    <div class="btn-group mb-3" role="group" aria-label="Default button group">
                        <button type="button" class="btn btn-dark">Left</button>
                        <button type="button" class="btn btn-dark">Middle</button>
                        <button type="button" class="btn btn-dark">Right</button>
                    </div>
                    <br>
                    <div class="btn-group btn-group-sm mb-3" role="group" aria-label="Small button group">
                        <button type="button" class="btn btn-dark">Left</button>
                        <button type="button" class="btn btn-dark">Middle</button>
                        <button type="button" class="btn btn-dark">Right</button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="btn-group btn-group-lg" role="group" aria-label="Large button group"&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Left&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Middle&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Right&lt;/button&gt;
&lt;/div&gt;

&lt;div class="btn-group" role="group" aria-label="Default button group"&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Left&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Middle&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Right&lt;/button&gt;
&lt;/div&gt;

&lt;div class="btn-group btn-group-sm" role="group" aria-label="Small button group"&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Left&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Middle&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;Right&lt;/button&gt;
&lt;/div&gt;</code></pre>
                </div>

                <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                    <button type="button" class="btn btn-dark">1</button>
                    <button type="button" class="btn btn-dark">2</button>

                    <div class="btn-group" role="group">
                        <button id="btnGroupDrop1" type="button" class="btn btn-dark dropdown-toggle"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Dropdown
                        </button>
                        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                            <a class="dropdown-item" href="#">Dropdown link</a>
                            <a class="dropdown-item" href="#">Dropdown link</a>
                        </div>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="btn-group" role="group" aria-label="Button group with nested dropdown"&gt;
  &lt;button type="button" class="btn btn-dark"&gt;1&lt;/button&gt;
  &lt;button type="button" class="btn btn-dark"&gt;2&lt;/button&gt;

  &lt;div class="btn-group" role="group"&gt;
    &lt;button id="btnGroupDrop1" type="button" class="btn btn-dark dropdown-toggle"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"&gt;
      Dropdown
    &lt;/button&gt;
    &lt;div class="dropdown-menu" aria-labelledby="btnGroupDrop1"&gt;
      &lt;a class="dropdown-item" href="#"&gt;Dropdown link&lt;/a&gt;
      &lt;a class="dropdown-item" href="#"&gt;Dropdown link&lt;/a&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>

                <div class="d-flex">
                    <div class="btn-group-vertical mr-3" role="group" aria-label="Vertical button group">
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-cloud-upload"></i>
                        </button>
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-calendar"></i>
                        </button>
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-database"></i>
                        </button>
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-cog"></i>
                        </button>
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-heart-o"></i>
                        </button>
                        <button type="button" class="btn btn-primary">
                            <i class="fa fa-share"></i>
                        </button>
                    </div>
                    <div class="btn-group-vertical mr-3" role="group" aria-label="Vertical button group">
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-cloud-upload"></i>
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-calendar"></i>
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-database"></i>
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-cog"></i>
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-heart-o"></i>
                        </button>
                        <button type="button" class="btn btn-success">
                            <i class="fa fa-share"></i>
                        </button>
                    </div>
                    <div class="btn-group-vertical mr-3" role="group" aria-label="Vertical button group">
                        <button type="button" class="btn btn-danger">
                            <i class="fa fa-cloud-upload"></i>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="fa fa-calendar"></i>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="fa fa-database"></i>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="fa fa-cog"></i>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="fa fa-heart-o"></i>
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="fa fa-share"></i>
                        </button>
                    </div>
                    <div class="btn-group-vertical mr-3" role="group" aria-label="Vertical button group">
                        <button type="button" class="btn btn-warning">
                            <i class="fa fa-cloud-upload"></i>
                        </button>
                        <button type="button" class="btn btn-warning">
                            <i class="fa fa-calendar"></i>
                        </button>
                        <button type="button" class="btn btn-warning">
                            <i class="fa fa-database"></i>
                        </button>
                        <button type="button" class="btn btn-warning">
                            <i class="fa fa-cog"></i>
                        </button>
                        <button type="button" class="btn btn-warning">
                            <i class="fa fa-heart-o"></i>
                        </button>
                        <button type="button" class="btn btn-warning">
                            <i class="fa fa-share"></i>
                        </button>
                    </div>
                    <div class="btn-group-vertical" role="group" aria-label="Vertical button group">
                        <button type="button" class="btn btn-info">
                            <i class="fa fa-cloud-upload"></i>
                        </button>
                        <button type="button" class="btn btn-info">
                            <i class="fa fa-calendar"></i>
                        </button>
                        <button type="button" class="btn btn-info">
                            <i class="fa fa-database"></i>
                        </button>
                        <button type="button" class="btn btn-info">
                            <i class="fa fa-cog"></i>
                        </button>
                        <button type="button" class="btn btn-info">
                            <i class="fa fa-heart-o"></i>
                        </button>
                        <button type="button" class="btn btn-info">
                            <i class="fa fa-share"></i>
                        </button>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="btn-group-vertical" role="group" aria-label="Vertical button group"&gt;
  &lt;button type="button" class="btn btn-primary"&gt;
    &lt;i class="fa fa-cloud-upload"&gt;&lt;/i&gt;
  &lt;/button&gt;
  &lt;button type="button" class="btn btn-primary"&gt;
    &lt;i class="fa fa-calendar"&gt;&lt;/i&gt;
  &lt;/button&gt;
  &lt;button type="button" class="btn btn-primary"&gt;
    &lt;i class="fa fa-database"&gt;&lt;/i&gt;
  &lt;/button&gt;
  &lt;button type="button" class="btn btn-primary"&gt;
    &lt;i class="fa fa-cog"&gt;&lt;/i&gt;
  &lt;/button&gt;
  &lt;button type="button" class="btn btn-primary"&gt;
    &lt;i class="fa fa-heart-o"&gt;&lt;/i&gt;
  &lt;/button&gt;
  &lt;button type="button" class="btn btn-primary"&gt;
    &lt;i class="fa fa-share"&gt;&lt;/i&gt;
  &lt;/button&gt;
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
