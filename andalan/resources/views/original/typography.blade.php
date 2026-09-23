@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Typography</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Typography</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6">

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Headings</h6>
                        <h1>h1. Bootstrap heading</h1>
                        <h2>h2. Bootstrap heading</h2>
                        <h3>h3. Bootstrap heading</h3>
                        <h4>h4. Bootstrap heading</h4>
                        <h5>h5. Bootstrap heading</h5>
                        <h6>h6. Bootstrap heading</h6>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;h1&gt;h1. Bootstrap heading&lt;/h1&gt;
&lt;h2&gt;h2. Bootstrap heading&lt;/h2&gt;
&lt;h3&gt;h3. Bootstrap heading&lt;/h3&gt;
&lt;h4&gt;h4. Bootstrap heading&lt;/h4&gt;
&lt;h5&gt;h5. Bootstrap heading&lt;/h5&gt;
&lt;h6&gt;h6. Bootstrap heading&lt;/h6&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Display headings</h6>
                        <h1 class="display-1">Display 1</h1>
                        <h1 class="display-2">Display 2</h1>
                        <h1 class="display-3">Display 3</h1>
                        <h1 class="display-4">Display 4</h1>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;h1 class="display-1"&gt;Display 1&lt;/h1&gt;
&lt;h1 class="display-2"&gt;Display 2&lt;/h1&gt;
&lt;h1 class="display-3"&gt;Display 3&lt;/h1&gt;
&lt;h1 class="display-4"&gt;Display 4&lt;/h1&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4>Text Utilities</h4>
                        <hr>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Abbreviations</div>
                    <div class="card-body">
                        <p><abbr title="attribute">attr</abbr></p>
                        <p><abbr title="HyperText Markup Language" class="initialism">HTML</abbr></p>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;abbr title="attribute"&gt;attr&lt;/abbr&gt;
&lt;abbr title="HyperText Markup Language" class="initialism"&gt;HTML&lt;/abbr&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Blockquotes</div>
                    <div class="card-body">
                        <blockquote class="blockquote">
                            <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere
                                erat a ante.</p>
                        </blockquote>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-html">&lt;blockquote class="blockquote"&gt;...&lt;/blockquote&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Naming a source</div>
                    <div class="card-body">
                        <blockquote class="blockquote">
                            <p class="mb-0">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere
                                erat a ante.</p>
                            <footer class="blockquote-footer">Someone famous in <cite title="Source Title">Source
                                    Title</cite></footer>
                        </blockquote>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;blockquote class="blockquote"&gt;
  ...
  &lt;footer class="blockquote-footer"&gt;
    ...
    &lt;cite title="Source Title"&gt;...&lt;/cite&gt;
  &lt;/footer&gt;
&lt;/blockquote&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Description list alignment</div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Description lists</dt>
                            <dd class="col-sm-9">A description list is perfect for defining terms.</dd>

                            <dt class="col-sm-3">Euismod</dt>
                            <dd class="col-sm-9">
                                <p>Vestibulum id ligula porta felis euismod semper eget lacinia odio sem nec
                                    elit.</p>
                                <p>Donec id elit non mi porta gravida at eget metus.</p>
                            </dd>

                            <dt class="col-sm-3">Malesuada porta</dt>
                            <dd class="col-sm-9">Etiam porta sem malesuada magna mollis euismod.</dd>

                            <dt class="col-sm-3 text-truncate">Truncated term is truncated</dt>
                            <dd class="col-sm-9">Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum
                                nibh, ut fermentum massa justo sit amet risus.
                            </dd>

                            <dt class="col-sm-3">Nesting</dt>
                            <dd class="col-sm-9">
                                <dl class="row">
                                    <dt class="col-sm-4">Nested definition list</dt>
                                    <dd class="col-sm-8">Aenean posuere, tortor sed cursus feugiat, nunc augue
                                        blandit
                                        nunc.
                                    </dd>
                                </dl>
                            </dd>
                        </dl>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;dl class="row"&gt;
  &lt;dt class="col-sm-3"&gt;...&lt;/dt&gt;
  &lt;dd class="col-sm-9"&gt;...&lt;/dd&gt;

  &lt;dt class="col-sm-3"&gt;Nesting&lt;/dt&gt;
  &lt;dd class="col-sm-9"&gt;
    &lt;dl class="row"&gt;
      &lt;dt class="col-sm-4"&gt;...&lt;/dt&gt;
      &lt;dd class="col-sm-8"&gt;...&lt;/dd&gt;
    &lt;/dl&gt;
  &lt;/dd&gt;
&lt;/dl&gt;</code></pre>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-6">

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Customizing headings</h6>
                        <h3>
                            Fancy display heading
                            <small class="text-muted">With faded secondary text</small>
                        </h3>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;h3&gt;
  Fancy display heading
  &lt;small class="text-muted"&gt;With faded secondary text&lt;/small&gt;
&lt;/h3&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Lead</h6>
                        <p class="lead">
                            Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est
                            non
                            commodo luctus.
                        </p>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-html">&lt;p class="lead"&gt;...&lt;/p&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Inline text elements</h6>
                        <p>You can use the mark tag to
                            <mark>highlight</mark>
                            text.
                        </p>
                        <p>
                            <del>This line of text is meant to be treated as deleted text.</del>
                        </p>
                        <p><s>This line of text is meant to be treated as no longer accurate.</s></p>
                        <p>
                            <ins>This line of text is meant to be treated as an addition to the document.</ins>
                        </p>
                        <p><u>This line of text will render as underlined</u></p>
                        <p>
                            <small>This line of text is meant to be treated as fine print.</small>
                        </p>
                        <p><strong>This line rendered as bold text.</strong></p>
                        <p><em>This line rendered as italicized text.</em></p>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;p&gt;You can use the mark tag to
  &lt;mark&gt;highlight&lt;/mark&gt;
  text.
&lt;/p&gt;
&lt;p&gt;
  &lt;del&gt;This line of text is meant to be treated as deleted text.&lt;/del&gt;
&lt;/p&gt;
&lt;p&gt;&lt;s&gt;This line of text is meant to be treated as no longer accurate.&lt;/s&gt;&lt;/p&gt;
&lt;p&gt;
  &lt;ins&gt;This line of text is meant to be treated as an addition to the document.&lt;/ins&gt;
&lt;/p&gt;
&lt;p&gt;&lt;u&gt;This line of text will render as underlined&lt;/u&gt;&lt;/p&gt;
&lt;p&gt;
  &lt;small&gt;This line of text is meant to be treated as fine print.&lt;/small&gt;
&lt;/p&gt;
&lt;p&gt;&lt;strong&gt;This line rendered as bold text.&lt;/strong&gt;&lt;/p&gt;
&lt;p&gt;&lt;em&gt;This line rendered as italicized text.&lt;/em&gt;&lt;/p&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4>Lists</h4>
                        <hr>
                    </div>
                </div>

                <div class="card">
                    <h6 class="card-title">Unstyled</h6>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li>Lorem ipsum dolor sit amet</li>
                            <li>Consectetur adipiscing elit</li>
                            <li>Integer molestie lorem at massa</li>
                            <li>Facilisis in pretium nisl aliquet</li>
                            <li>Nulla volutpat aliquam velit
                                <ul>
                                    <li>Phasellus iaculis neque</li>
                                    <li>Purus sodales ultricies</li>
                                    <li>Vestibulum laoreet porttitor sem</li>
                                    <li>Ac tristique libero volutpat at</li>
                                </ul>
                            </li>
                            <li>Faucibus porta lacus fringilla vel</li>
                            <li>Aenean sit amet erat nunc</li>
                            <li>Eget porttitor lorem</li>
                        </ul>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;ul class="list-unstyled"&gt;
  &lt;li&gt;...&lt;/li&gt;
  &lt;li&gt;...&lt;/li&gt;
  &lt;li&gt;...&lt;/li&gt;
  &lt;li&gt;...&lt;/li&gt;
  &lt;li&gt; ...
    &lt;ul&gt;
      &lt;li&gt;...&lt;/li&gt;
      &lt;li&gt;...&lt;/li&gt;
      &lt;li&gt;...&lt;/li&gt;
      &lt;li&gt;...&lt;/li&gt;
    &lt;/ul&gt;
  &lt;/li&gt;
  &lt;li&gt;...&lt;/li&gt;
  &lt;li&gt;...&lt;/li&gt;
  &lt;li&gt;...&lt;/li&gt;
&lt;/ul&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">Inline</div>
                    <div class="card-body">
                        <ul class="list-inline">
                            <li class="list-inline-item">Lorem ipsum</li>
                            <li class="list-inline-item">Phasellus iaculis</li>
                            <li class="list-inline-item">Nulla volutpat</li>
                        </ul>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;ul class="list-inline"&gt;
  &lt;li class="list-inline-item"&gt;...&lt;/li&gt;
  &lt;li class="list-inline-item"&gt;...&lt;/li&gt;
  &lt;li class="list-inline-item"&gt;...&lt;/li&gt;
&lt;/ul&gt;</code></pre>
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
