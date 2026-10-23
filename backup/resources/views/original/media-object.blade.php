@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Media Object</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Media Object</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Media</h6>
                <div class="media">
                    <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" class="mr-3" alt="image">
                    <div class="media-body">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.
                        Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac
                        nisi
                        vulputate fringilla. Donec lacinia congue felis in faucibus.
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="media"&gt;
  &lt;img src="..." class="mr-3" alt="..."&gt;
  &lt;div class="media-body"&gt;
    &lt;h5 class="mt-0"&gt;Media heading&lt;/h5&gt;
    Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Nesting</h6>
                <div class="media">
                    <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" class="mr-3" alt="image">
                    <div class="media-body">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.
                        Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac
                        nisi
                        vulputate fringilla. Donec lacinia congue felis in faucibus.

                        <div class="media mt-3">
                            <a class="mr-3" href="#">
                                <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" alt="image">
                            </a>
                            <div class="media-body">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis.
                                Fusce
                                condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in
                                faucibus.
                            </div>
                        </div>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="media"&gt;
  &lt;img src="..." class="mr-3" alt="..."&gt;
  &lt;div class="media-body"&gt;
    &lt;h5 class="mt-0"&gt;Media heading&lt;/h5&gt;
    Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.

    &lt;div class="media mt-3"&gt;
      &lt;a class="mr-3" href="#"&gt;
        &lt;img src="..." class="mr-3" alt="..."&gt;
      &lt;/a&gt;
      &lt;div class="media-body"&gt;
        &lt;h5 class="mt-0"&gt;Media heading&lt;/h5&gt;
        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Media Avatar</h6>
                <div class="media">
                    <figure class="avatar mr-3">
                        <span class="avatar-title bg-danger rounded-circle">l</span>
                    </figure>
                    <div class="media-body">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.
                        Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac
                        nisi
                        vulputate fringilla. Donec lacinia congue felis in faucibus.

                        <div class="media mt-3 mb-3">
                            <a class="mr-3" href="#">
                                <figure class="avatar">
                                    <span class="avatar-title bg-success rounded-circle">K</span>
                                </figure>
                            </a>
                            <div class="media-body">
                                <h5 class="mt-0">Media heading</h5>
                                Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                                sollicitudin. Cras purus odio, vestibulum in vulputate at, tempus viverra turpis.
                                Fusce
                                condimentum nunc ac nisi vulputate fringilla. Donec lacinia congue felis in
                                faucibus.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="media">
                    <figure class="avatar mr-3">
                        <img src="{{ url('assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle" alt="image">
                    </figure>
                    <div class="media-body">
                        <h5 class="mt-0">Media heading</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.
                        Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac
                        nisi
                        vulputate fringilla. Donec lacinia congue felis in faucibus.
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="media"&gt;
  &lt;figure class="avatar mr-3"&gt;
    &lt;span class="avatar-title bg-danger rounded-circle"&gt;l&lt;/span&gt;
  &lt;/figure&gt;
  &lt;div class="media-body"&gt;
    &lt;h5 class="mt-0"&gt;Media heading&lt;/h5&gt;
    Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.

    &lt;div class="media mt-3 mb-3"&gt;
      &lt;a class="mr-3" href="#"&gt;
        &lt;figure class="avatar"&gt;
          &lt;span class="avatar-title bg-success rounded-circle"&gt;K&lt;/span&gt;
        &lt;/figure&gt;
      &lt;/a&gt;
      &lt;div class="media-body"&gt;
        &lt;h5 class="mt-0"&gt;Media heading&lt;/h5&gt;
        Cras sit amet nibh libero, in gravida nulla.
      &lt;/div&gt;
    &lt;/div&gt;

  &lt;/div&gt;
&lt;/div&gt;

&lt;div class="media"&gt;
  &lt;figure class="avatar mr-3"&gt;
    &lt;img src="avatar.jpg" class="rounded-circle" alt="image"&gt;
      &lt;/figure&gt;
    &lt;div class="media-body"&gt;
      &lt;h5 class="mt-0"&gt;Media heading&lt;/h5&gt;
      Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Alignment</h6>
                <div class="media">
                    <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" class="align-self-start mr-3" alt="image">
                    <div class="media-body">
                        <h5 class="mt-0">Top-aligned media</h5>
                        <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                            sollicitudin.
                            Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum
                            nunc
                            ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                        <p class="mb-0">Donec sed odio dui. Nullam quis risus eget urna mollis ornare vel eu leo.
                            Cum sociis
                            natoque
                            penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="media"&gt;
  &lt;img src="image.jpg" class="align-self-start mr-3" alt="image"&gt;
    &lt;div class="media-body"&gt;
      &lt;h5 class="mt-0"&gt;Top-aligned media&lt;/h5&gt;
      &lt;p&gt;Cras sit amet nibh libero, in gravida nulla.&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
                <div class="media">
                    <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" class="align-self-center mr-3" alt="image">
                    <div class="media-body">
                        <h5 class="mt-0">Center-aligned media</h5>
                        <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                            sollicitudin.
                            Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum
                            nunc
                            ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                        <p class="mb-0">Donec sed odio dui. Nullam quis risus eget urna mollis ornare vel eu leo.
                            Cum
                            sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="media"&gt;
  &lt;img src="image.jpg" class="align-self-center mr-3" alt="image"&gt;
    &lt;div class="media-body"&gt;
      &lt;h5 class="mt-0"&gt;Center-aligned media&lt;/h5&gt;
      &lt;p&gt;Cras sit amet nibh libero, in gravida nulla.&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
                <div class="media">
                    <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" class="align-self-end mr-3" alt="image">
                    <div class="media-body">
                        <h5 class="mt-0">Bottom-aligned media</h5>
                        <p>Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante
                            sollicitudin.
                            Cras purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum
                            nunc
                            ac nisi vulputate fringilla. Donec lacinia congue felis in faucibus.</p>
                        <p class="mb-0">Donec sed odio dui. Nullam quis risus eget urna mollis ornare vel eu leo.
                            Cum
                            sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.</p>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="media"&gt;
  &lt;img src="image.jpg" class="align-self-end mr-3" alt="image"&gt;
    &lt;div class="media-body"&gt;
      &lt;h5 class="mt-0"&gt;Bottom-aligned media&lt;/h5&gt;
      &lt;p&gt;Cras sit amet nibh libero, in gravida nulla.&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Order</h6>
                <div class="media">
                    <div class="media-body">
                        <h5 class="mt-0 mb-1">Media object</h5>
                        Cras sit amet nibh libero, in gravida nulla. Nulla vel metus scelerisque ante sollicitudin.
                        Cras
                        purus odio, vestibulum in vulputate at, tempus viverra turpis. Fusce condimentum nunc ac
                        nisi
                        vulputate fringilla. Donec lacinia congue felis in faucibus.
                    </div>
                    <img src="{{ url('assets/media/image/portfolio-one.jpg') }}" class="ml-3" alt="image">
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="media"&gt;
  &lt;div class="media-body"&gt;
    &lt;h5 class="mt-0 mb-1"&gt;Media object&lt;/h5&gt;
    &lt;p&gt;Cras sit amet nibh libero, in gravida nulla.&lt;/p&gt;
  &lt;/div&gt;
  &lt;img src="image.jpg" class="ml-3" alt="image"&gt;
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
