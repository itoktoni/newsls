@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Cards</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Cards</li>
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
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border">
                                    <img src="{{ url('assets/media/image/photo1.jpg') }}" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <p class="card-text">Some quick example text to build on the card title and
                                            make up the bulk of the card's content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;img src="image.jpg" class="card-img-top" alt="..."&gt;
    &lt;div class="card-body"&gt;
      &lt;h5 class="card-title"&gt;Card title&lt;/h5&gt;
      &lt;p class="card-text"&gt;Some quick example text to build on the card title and make up the bulk of the card's content.&lt;/p&gt;
      &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4>Content Types</h4>
                        <hr>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Body</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-body">
                                        This is some text within a card body.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-body"&gt;
    This is some text within a card body.
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <h6 class="card-title">Titles, text, and links</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-body">
                                        <h5 class="card-title">Card title</h5>
                                        <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
                                        <p class="card-text">Some quick example text to build on the card title and
                                            make up the bulk of the card's content.</p>
                                        <a href="#" class="card-link">Card link</a>
                                        <a href="#" class="card-link">Another link</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-body"&gt;
    &lt;h5 class="card-title"&gt;Card title&lt;/h5&gt;
    &lt;h6 class="card-subtitle mb-2 text-muted"&gt;Card subtitle&lt;/h6&gt;
    &lt;p class="card-text"&gt;Some quick example text to build on the card title and make up the bulk of the card's content.&lt;/p&gt;
    &lt;a href="#" class="card-link"&gt;Card link&lt;/a&gt;
    &lt;a href="#" class="card-link"&gt;Another link&lt;/a&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <h6 class="card-title">Images</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <img src="{{ url('assets/media/image/photo1.jpg') }}" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <p class="card-text">Some quick example text to build on the card title and
                                            make up the bulk of the card's content.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;img src="image.jpg" class="card-img-top" alt="..."&gt;
    &lt;div class="card-body"&gt;
      &lt;p class="card-text"&gt;Some quick example text to build on the card title and make up the bulk of the card's content.&lt;/p&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <h6 class="card-title">Body</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-body">
                                        This is some text within a card body.
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-body"&gt;
    This is some text within a card body.
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <h6 class="card-title">List groups</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Cras justo odio</li>
                                        <li class="list-group-item">Dapibus ac facilisis in</li>
                                        <li class="list-group-item">Vestibulum at eros</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;ul class="list-group list-group-flush"&gt;
    &lt;li class="list-group-item"&gt;Cras justo odio&lt;/li&gt;
    &lt;li class="list-group-item"&gt;Dapibus ac facilisis in&lt;/li&gt;
    &lt;li class="list-group-item"&gt;Vestibulum at eros&lt;/li&gt;
  &lt;/ul&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-header">
                                        Featured
                                    </div>
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">Cras justo odio</li>
                                        <li class="list-group-item">Dapibus ac facilisis in</li>
                                        <li class="list-group-item">Vestibulum at eros</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-header"&gt;
    Featured
  &lt;/div&gt;
  &lt;ul class="list-group list-group-flush"&gt;
    &lt;li class="list-group-item"&gt;Cras justo odio&lt;/li&gt;
    &lt;li class="list-group-item"&gt;Dapibus ac facilisis in&lt;/li&gt;
    &lt;li class="list-group-item"&gt;Vestibulum at eros&lt;/li&gt;
  &lt;/ul&gt;
&lt;/div&gt;</code></pre>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Header and footer</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-header">
                                        Featured
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Special title treatment</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to
                                            additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-header"&gt;
    Featured
  &lt;/div&gt;
  &lt;div class="card-body"&gt;
    &lt;h5 class="card-title"&gt;Special title treatment&lt;/h5&gt;
    &lt;p class="card-text"&gt;With supporting text below as a natural lead-in to additional content.&lt;/p&gt;
    &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-header">
                                        Quote
                                    </div>
                                    <div class="card-body">
                                        <blockquote class="blockquote mb-0">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer
                                                posuere erat a ante.</p>
                                            <footer class="blockquote-footer">Someone famous in <cite
                                                    title="Source Title">Source Title</cite></footer>
                                        </blockquote>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-header"&gt;
    Quote
  &lt;/div&gt;
  &lt;div class="card-body"&gt;
    &lt;blockquote class="blockquote mb-0"&gt;
      &lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante.&lt;/p&gt;
      &lt;footer class="blockquote-footer"&gt;Someone famous in &lt;cite title="Source Title"&gt;Source Title&lt;/cite&gt;&lt;/footer&gt;
    &lt;/blockquote&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border text-center mb-0">
                                    <div class="card-header">
                                        Featured
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">Special title treatment</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to
                                            additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                    <div class="card-footer text-muted">
                                        2 days ago
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card text-center"&gt;
  &lt;div class="card-header"&gt;
    Featured
  &lt;/div&gt;
  &lt;div class="card-body"&gt;
    &lt;h5 class="card-title"&gt;Special title treatment&lt;/h5&gt;
    &lt;p class="card-text"&gt;With supporting text below as a natural lead-in to additional content.&lt;/p&gt;
    &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
  &lt;/div&gt;
  &lt;div class="card-footer text-muted"&gt;
    2 days ago
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Using grid markup</h6>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card border mb-0">
                                    <div class="card-body">
                                        <h5 class="card-title">Special title treatment</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to
                                            additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card border mb-0">
                                    <div class="card-body">
                                        <h5 class="card-title">Special title treatment</h5>
                                        <p class="card-text">With supporting text below as a natural lead-in to
                                            additional content.</p>
                                        <a href="#" class="btn btn-primary">Go somewhere</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="row"&gt;
  &lt;div class="col-sm-6"&gt;
    &lt;div class="card"&gt;
      &lt;div class="card-body"&gt;
        &lt;h5 class="card-title"&gt;Special title treatment&lt;/h5&gt;
        &lt;p class="card-text"&gt;With supporting text below as a natural lead-in to additional content.&lt;/p&gt;
        &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="col-sm-6"&gt;
    &lt;div class="card"&gt;
      &lt;div class="card-body"&gt;
        &lt;h5 class="card-title"&gt;Special title treatment&lt;/h5&gt;
        &lt;p class="card-text"&gt;With supporting text below as a natural lead-in to additional content.&lt;/p&gt;
        &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Navigation</h6>
                        <div class="card text-center border">
                            <div class="card-header pb-0">
                                <ul class="nav nav-tabs card-header-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#">Active</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Link</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Special title treatment</h5>
                                <p class="card-text">With supporting text below as a natural lead-in to additional
                                    content.</p>
                                <a href="#" class="btn btn-primary">Go somewhere</a>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card text-center"&gt;
  &lt;div class="card-header pb-0"&gt;
    &lt;ul class="nav nav-tabs card-header-tabs"&gt;
      &lt;li class="nav-item"&gt;
        &lt;a class="nav-link active" href="#"&gt;Active&lt;/a&gt;
      &lt;/li&gt;
      &lt;li class="nav-item"&gt;
        &lt;a class="nav-link" href="#"&gt;Link&lt;/a&gt;
      &lt;/li&gt;
      &lt;li class="nav-item"&gt;
        &lt;a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"&gt;Disabled&lt;/a&gt;
      &lt;/li&gt;
    &lt;/ul&gt;
  &lt;/div&gt;
  &lt;div class="card-body"&gt;
    &lt;h5 class="card-title"&gt;Special title treatment&lt;/h5&gt;
    &lt;p class="card-text"&gt;With supporting text below as a natural lead-in to additional content.&lt;/p&gt;
    &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <div class="card border text-center">
                            <div class="card-header">
                                <ul class="nav nav-pills card-header-pills">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#">Active</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#">Link</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">Special title treatment</h5>
                                <p class="card-text">With supporting text below as a natural lead-in to additional
                                    content.</p>
                                <a href="#" class="btn btn-primary">Go somewhere</a>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card text-center"&gt;
  &lt;div class="card-header"&gt;
    &lt;ul class="nav nav-pills card-header-pills"&gt;
      &lt;li class="nav-item"&gt;
        &lt;a class="nav-link active" href="#"&gt;Active&lt;/a&gt;
      &lt;/li&gt;
      &lt;li class="nav-item"&gt;
        &lt;a class="nav-link" href="#"&gt;Link&lt;/a&gt;
      &lt;/li&gt;
      &lt;li class="nav-item"&gt;
        &lt;a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true"&gt;Disabled&lt;/a&gt;
      &lt;/li&gt;
    &lt;/ul&gt;
  &lt;/div&gt;
  &lt;div class="card-body"&gt;
    &lt;h5 class="card-title"&gt;Special title treatment&lt;/h5&gt;
    &lt;p class="card-text"&gt;With supporting text below as a natural lead-in to additional content.&lt;/p&gt;
    &lt;a href="#" class="btn btn-primary"&gt;Go somewhere&lt;/a&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
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
