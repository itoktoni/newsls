@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Card Scroll</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Card Scroll</li>
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
                                <div class="card border mb-0">
                                    <div class="card-body">
                                        <div class="card-scroll">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi
                                                architecto beatae commodi
                                                deserunt dolores eaque earum, esse et eveniet excepturi id labore
                                                magni maxime modi nemo
                                                nesciunt nobis non, nostrum perferendis placeat praesentium repellat
                                                rerum voluptatum!
                                                Eos id nisi obcaecati reprehenderit. Assumenda culpa illum quis
                                                sapiente temporibus. Ad
                                                blanditiis cum ducimus fugiat ipsa ipsam magnam maiores maxime nemo
                                                officia quas qui
                                                quibusdam unde veritatis voluptas, voluptates voluptatum? Ab ad,
                                                asperiores distinctio
                                                dolorem eaque exercitationem facilis ipsa ipsum natus, neque nihil
                                                perferendis,
                                                praesentium quisquam quod suscipit vitae voluptas. Accusamus
                                                deserunt dolore eos
                                                maiores, nisi porro repellendus velit veniam veritatis, vero
                                                voluptas. Lorem ipsum dolor
                                                sit amet, consectetur adipisicing elit. Animi architecto beatae
                                                commodi deserunt dolores
                                                eaque earum, esse et eveniet excepturi id labore magni maxime modi
                                                nemo nesciunt nobis
                                                non, nostrum perferendis placeat praesentium repellat rerum
                                                voluptatum! Eos id nisi
                                                obcaecati reprehenderit. Assumenda culpa illum quis sapiente
                                                temporibus. Ad blanditiis
                                                cum ducimus fugiat ipsa ipsam magnam maiores maxime nemo officia
                                                quas qui quibusdam unde
                                                veritatis voluptas, voluptates voluptatum? Ab ad, asperiores
                                                distinctio dolorem eaque
                                                exercitationem facilis ipsa ipsum natus, neque nihil perferendis,
                                                praesentium quisquam
                                                quod suscipit vitae voluptas. Accusamus deserunt dolore eos maiores,
                                                nisi porro
                                                repellendus velit veniam veritatis, vero voluptas.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-body"&gt;
    &lt;div class="card-scroll"&gt;
      ...
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-header">Card Header</div>
                                    <div class="card-body">
                                        <div class="card-scroll">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi
                                                architecto beatae commodi
                                                deserunt dolores eaque earum, esse et eveniet excepturi id labore
                                                magni maxime modi nemo
                                                nesciunt nobis non, nostrum perferendis placeat praesentium repellat
                                                rerum voluptatum!
                                                Eos id nisi obcaecati reprehenderit. Assumenda culpa illum quis
                                                sapiente temporibus. Ad
                                                blanditiis cum ducimus fugiat ipsa ipsam magnam maiores maxime nemo
                                                officia quas qui
                                                quibusdam unde veritatis voluptas, voluptates voluptatum? Ab ad,
                                                asperiores distinctio
                                                dolorem eaque exercitationem facilis ipsa ipsum natus, neque nihil
                                                perferendis,
                                                praesentium quisquam quod suscipit vitae voluptas. Accusamus
                                                deserunt dolore eos
                                                maiores, nisi porro repellendus velit veniam veritatis, vero
                                                voluptas. Lorem ipsum dolor
                                                sit amet, consectetur adipisicing elit. Animi architecto beatae
                                                commodi deserunt dolores
                                                eaque earum, esse et eveniet excepturi id labore magni maxime modi
                                                nemo nesciunt nobis
                                                non, nostrum perferendis placeat praesentium repellat rerum
                                                voluptatum! Eos id nisi
                                                obcaecati reprehenderit. Assumenda culpa illum quis sapiente
                                                temporibus. Ad blanditiis
                                                cum ducimus fugiat ipsa ipsam magnam maiores maxime nemo officia
                                                quas qui quibusdam unde
                                                veritatis voluptas, voluptates voluptatum? Ab ad, asperiores
                                                distinctio dolorem eaque
                                                exercitationem facilis ipsa ipsum natus, neque nihil perferendis,
                                                praesentium quisquam
                                                quod suscipit vitae voluptas. Accusamus deserunt dolore eos maiores,
                                                nisi porro
                                                repellendus velit veniam veritatis, vero voluptas.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-header"&gt;Card Header&lt;/div&gt;
  &lt;div class="card-body"&gt;
    &lt;div class="card-scroll"&gt;
      ...
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card border mb-0">
                                    <div class="card-header">Card Header</div>
                                    <div class="card-body">
                                        <div class="card-scroll">
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Animi
                                                architecto beatae commodi
                                                deserunt dolores eaque earum, esse et eveniet excepturi id labore
                                                magni maxime modi nemo
                                                nesciunt nobis non, nostrum perferendis placeat praesentium repellat
                                                rerum voluptatum!
                                                Eos id nisi obcaecati reprehenderit. Assumenda culpa illum quis
                                                sapiente temporibus. Ad
                                                blanditiis cum ducimus fugiat ipsa ipsam magnam maiores maxime nemo
                                                officia quas qui
                                                quibusdam unde veritatis voluptas, voluptates voluptatum? Ab ad,
                                                asperiores distinctio
                                                dolorem eaque exercitationem facilis ipsa ipsum natus, neque nihil
                                                perferendis,
                                                praesentium quisquam quod suscipit vitae voluptas. Accusamus
                                                deserunt dolore eos
                                                maiores, nisi porro repellendus velit veniam veritatis, vero
                                                voluptas. Lorem ipsum dolor
                                                sit amet, consectetur adipisicing elit. Animi architecto beatae
                                                commodi deserunt dolores
                                                eaque earum, esse et eveniet excepturi id labore magni maxime modi
                                                nemo nesciunt nobis
                                                non, nostrum perferendis placeat praesentium repellat rerum
                                                voluptatum! Eos id nisi
                                                obcaecati reprehenderit. Assumenda culpa illum quis sapiente
                                                temporibus. Ad blanditiis
                                                cum ducimus fugiat ipsa ipsam magnam maiores maxime nemo officia
                                                quas qui quibusdam unde
                                                veritatis voluptas, voluptates voluptatum? Ab ad, asperiores
                                                distinctio dolorem eaque
                                                exercitationem facilis ipsa ipsum natus, neque nihil perferendis,
                                                praesentium quisquam
                                                quod suscipit vitae voluptas. Accusamus deserunt dolore eos maiores,
                                                nisi porro
                                                repellendus velit veniam veritatis, vero voluptas.</p>
                                        </div>
                                    </div>
                                    <div class="card-footer">Card Footer</div>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="card"&gt;
  &lt;div class="card-header"&gt;Card Header&lt;/div&gt;
  &lt;div class="card-body"&gt;
    &lt;div class="card-scroll"&gt;
      ...
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="card-footer"&gt;Card Footer&lt;/div&gt;
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
