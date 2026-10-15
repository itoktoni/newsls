@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Modal</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Modal</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="card">
            <div class="card-body">

                <div class="modal" tabindex="-1" role="dialog" style="position: static;display: block">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content border">
                            <div class="modal-header">
                                <h5 class="modal-title">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Modal body text goes here.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="modal" tabindex="-1" role="dialog"&gt;
  &lt;div class="modal-dialog" role="document"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h5 class="modal-title"&gt;Modal title&lt;/h5&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;span aria-hidden="true"&gt;&times;&lt;/span&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        &lt;p&gt;Modal body text goes here.&lt;/p&gt;
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close&lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Live Demo</h6>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal1">
                    Launch demo modal
                </button>

                <!-- Modal 1 -->
                <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModal1Label" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModal1Label">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ...
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary mr-2 mb-2" data-toggle="modal" data-target="#exampleModal"&gt;
  Launch demo modal
&lt;/button&gt;

&lt;div class="modal" tabindex="-1" role="dialog" id="exampleModal"&gt;
  &lt;div class="modal-dialog" role="document"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h5 class="modal-title"&gt;Modal title&lt;/h5&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;span aria-hidden="true"&gt;×&lt;/span&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        &lt;p&gt;Modal body text goes here.&lt;/p&gt;
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close&lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Scrolling long content</h6>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalLong">
                    Launch demo modal
                </button>

                <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog " role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="ti-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab amet, atque
                                autem, commodi consequatur corporis cumque ea eligendi esse ex facere fugit
                                harum incidunt iure laborum maiores minus non pariatur praesentium quod
                                sapiente tenetur ullam unde vel voluptatibus. Accusamus aliquid beatae
                                dolore earum incidunt nulla possimus quibusdam, sed? Accusantium, impedit,
                                voluptatum! Accusamus asperiores aut cupiditate, debitis deleniti ea, enim
                                eveniet explicabo facilis inventore iusto maiores molestiae natus nostrum,
                                quod tempora temporibus. Blanditiis consequuntur dignissimos ducimus eos
                                expedita facere fugit, incidunt laudantium libero natus odit recusandae rem
                                sequi sunt temporibus, vel velit, voluptate! Dolores, nesciunt quod! Amet
                                corporis eaque esse excepturi maiores nesciunt officiis sed temporibus vitae
                                voluptas voluptatem, voluptatibus! Accusamus animi aperiam autem
                                consequatur, cupiditate distinctio dolore ducimus eius est ex explicabo id
                                itaque molestiae natus nostrum nulla, odit, officiis quam qui rerum sunt
                                suscipit temporibus vero. Ab adipisci aliquam aspernatur beatae blanditiis
                                cum delectus doloribus enim expedita facere impedit in laborum magnam, magni
                                nam, necessitatibus odio, quasi repudiandae. Deserunt doloribus esse itaque
                                magnam nihil nobis possimus quidem repellendus? Aliquid, amet consequuntur
                                dicta dignissimos dolore dolorem et, in nam neque officiis quisquam tempore.
                                Eligendi et hic officiis quam quisquam veniam veritatis. A doloribus natus
                                quisquam veniam! Beatae culpa ducimus earum exercitationem odio quae quam
                                quas quia quidem, quo recusandae, repudiandae sapiente sed temporibus
                                tenetur totam ut. Alias aspernatur at beatae consequatur cupiditate error
                                ipsam natus nobis numquam pariatur quaerat quia quisquam, quos, ratione
                                vero. Ab assumenda commodi consequatur deleniti doloribus eum excepturi
                                expedita facere iusto magnam maxime minus molestiae, nesciunt nostrum
                                numquam obcaecati odio officia officiis perferendis possimus quam quod
                                repellendus ullam voluptates voluptatum! Accusamus accusantium aliquam
                                aliquid architecto at atque autem beatae blanditiis commodi consectetur
                                dolores eius eligendi eos, eum incidunt magnam magni maiores odio possimus
                                quae quaerat quas quasi quisquam ratione reiciendis similique sit tempore
                                velit voluptate voluptates!
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                </button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="modal" tabindex="-1" role="dialog"&gt;
  &lt;div class="modal-dialog" role="document"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h5 class="modal-title"&gt;Modal title&lt;/h5&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;span aria-hidden="true"&gt;&times;&lt;/span&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        ...
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close&lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Vertically centered</h6>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                    Vertically centered modal
                </button>

                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalCenterTitle">Modal title</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="ti-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac
                                    facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac,
                                    vestibulum at eros.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                </button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-hidden="true"&gt;
  &lt;div class="modal-dialog modal-dialog-centered" role="document"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h5 class="modal-title" id="exampleModalCenterTitle"&gt;Modal title&lt;/h5&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;i class="ti-close"&gt;&lt;/i&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        ...
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close
        &lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Optional sizes</h6>

                <div>
                    <button type="button" class="btn btn-primary mr-2 mb-2" data-toggle="modal"
                            data-target=".bd-example-modal-xl">Extra large modal
                    </button>
                    <button type="button" class="btn btn-primary mr-2 mb-2" data-toggle="modal"
                            data-target=".bd-example-modal-lg">Large modal
                    </button>
                    <button type="button" class="btn btn-primary mb-2" data-toggle="modal"
                            data-target=".bd-example-modal-sm">Small modal
                    </button>
                </div>

                <div class="modal fade bd-example-modal-xl" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Extra large modal</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="ti-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                ...
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                </button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Large modal</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="ti-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                ...
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                </button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Small modal</h6>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="ti-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                ...
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                </button>
                                <button type="button" class="btn btn-primary">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;!-- .modal-xl --&gt;
&lt;div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"&gt;
  &lt;div class="modal-dialog modal-xl"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h6 class="modal-title"&gt;Extra large modal&lt;/h6&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;i class="ti-close"&gt;&lt;/i&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        ...
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close
        &lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;

&lt;!-- .modal-lg --&gt;
&lt;div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"&gt;
  &lt;div class="modal-dialog modal-lg"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h6 class="modal-title"&gt;Large modal&lt;/h6&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;i class="ti-close"&gt;&lt;/i&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        ...
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close
        &lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;

&lt;!-- .modal-sm --&gt;
&lt;div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true"&gt;
  &lt;div class="modal-dialog modal-sm"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h6 class="modal-title"&gt;Small modal&lt;/h6&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;i class="ti-close"&gt;&lt;/i&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        ...
      &lt;/div&gt;
      &lt;div class="modal-footer"&gt;
        &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close
        &lt;/button&gt;
        &lt;button type="button" class="btn btn-primary"&gt;Save changes&lt;/button&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                </div>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Varying modal content</h6>

                <div>
                    <button type="button" class="btn btn-primary mr-2 mb-2" data-toggle="modal"
                            data-target="#exampleModal" data-whatever="@mdo">Open modal for @mdo
                    </button>
                    <button type="button" class="btn btn-primary mr-2 mb-2" data-toggle="modal"
                            data-target="#exampleModal" data-whatever="@fat">Open modal for @fat
                    </button>
                    <button type="button" class="btn btn-primary mb-2" data-toggle="modal"
                            data-target="#exampleModal" data-whatever="@getbootstrap">Open modal for
                        @getbootstrap
                    </button>
                </div>

                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <i class="ti-close"></i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form>
                                    <div class="form-group">
                                        <label for="recipient-name" class="col-form-label">Recipient:</label>
                                        <input type="text" class="form-control" id="recipient-name">
                                    </div>
                                    <div class="form-group">
                                        <label for="message-text" class="col-form-label">Message:</label>
                                        <textarea class="form-control" id="message-text"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close
                                </button>
                                <button type="button" class="btn btn-primary">Send message</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo"&gt;
  Open modal for @mdo
&lt;/button&gt;

&lt;button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@fat"&gt;
  Open modal for @fat
&lt;/button&gt;

&lt;button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@getbootstrap"&gt;Open modal for
  @getbootstrap
&lt;/button&gt;

&lt;!-- modal --&gt;
&lt;div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true"&gt;
  &lt;div class="modal-dialog" role="document"&gt;
    &lt;div class="modal-content"&gt;
      &lt;div class="modal-header"&gt;
        &lt;h5 class="modal-title" id="exampleModalLabel"&gt;New message&lt;/h5&gt;
        &lt;button type="button" class="close" data-dismiss="modal" aria-label="Close"&gt;
          &lt;i class="ti-close"&gt;&lt;/i&gt;
        &lt;/button&gt;
      &lt;/div&gt;
      &lt;div class="modal-body"&gt;
        &lt;form&gt;
          &lt;div class="form-group"&gt;
            &lt;label for="recipient-name" class="col-form-label"&gt;Recipient:&lt;/label&gt;
            &lt;input type="text" class="form-control" id="recipient-name"&gt;
          &lt;/div&gt;
          &lt;div class="form-group"&gt;
              &lt;label for="message-text" class="col-form-label"&gt;Message:&lt;/label&gt;
              &lt;textarea class="form-control" id="message-text"&gt;&lt;/textarea&gt;
            &lt;/div&gt;
         &lt;/form&gt;
       &lt;/div&gt;
       &lt;div class="modal-footer"&gt;
          &lt;button type="button" class="btn btn-secondary" data-dismiss="modal"&gt;Close
          &lt;/button&gt;
          &lt;button type="button" class="btn btn-primary"&gt;Send message&lt;/button&gt;
       &lt;/div&gt;
     &lt;/div&gt;
  &lt;/div&gt;
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
