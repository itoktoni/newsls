@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Tab</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tab</li>
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
                        <h6 class="card-title">Basic tab</h6>
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
                                   aria-controls="home" aria-selected="true">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
                                   aria-controls="profile" aria-selected="false">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab"
                                   aria-controls="contact" aria-selected="false">Contact</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                 aria-labelledby="home-tab">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis, soluta!
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                Lorem
                                ipsum dolor sit amet, consectetur adipisicing elit. Accusantium commodi error ipsa
                                ipsam
                                sapiente tempore.
                            </div>
                            <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                Lorem
                                ipsum dolor sit amet.
                            </div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;ul class="nav nav-tabs mb-3" role="tablist"&gt;
  &lt;li class="nav-item"&gt;
    &lt;a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab"
       aria-controls="home" aria-selected="true"&gt;Home&lt;/a&gt;
  &lt;/li&gt;
  &lt;li class="nav-item"&gt;
    &lt;a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab"
       aria-controls="profile" aria-selected="false"&gt;Profile&lt;/a&gt;
  &lt;/li&gt;
  &lt;li class="nav-item"&gt;
    &lt;a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab"
       aria-controls="contact" aria-selected="false"&gt;Contact&lt;/a&gt;
  &lt;/li&gt;
&lt;/ul&gt;
&lt;div class="tab-content"&gt;
  &lt;div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab"&gt;
    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis, soluta!
  &lt;/div&gt;
  &lt;div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab"&gt;Lorem
    ipsum dolor sit amet, consectetur adipisicing elit. Accusantium commodi error ipsa ipsam
    sapiente tempore.
  &lt;/div&gt;
  &lt;div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab"&gt;Lorem
    ipsum dolor sit amet.
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Pill tab</h6>
                        <ul class="nav nav-pills mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
                                   role="tab" aria-controls="pills-home" aria-selected="true">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
                                   role="tab" aria-controls="pills-profile" aria-selected="false">Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact"
                                   role="tab" aria-controls="pills-contact" aria-selected="false">Contact</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                 aria-labelledby="pills-home-tab"> Lorem ipsum dolor sit amet, consectetur
                                adipisicing elit. Harum, soluta.
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                 aria-labelledby="pills-profile-tab"> Lorem ipsum dolor sit amet, consectetur
                                adipisicing elit. Harum, soluta.
                            </div>
                            <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                 aria-labelledby="pills-contact-tab"> Lorem ipsum dolor sit amet, consectetur
                                adipisicing elit. Harum, soluta.
                            </div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;ul class="nav nav-pills mb-3" role="tablist"&gt;
  &lt;li class="nav-item"&gt;
    &lt;a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home"
       role="tab" aria-controls="pills-home" aria-selected="true"&gt;Home&lt;/a&gt;
  &lt;/li&gt;
  &lt;li class="nav-item"&gt;
    &lt;a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile"
       role="tab" aria-controls="pills-profile" aria-selected="false"&gt;Profile&lt;/a&gt;
  &lt;/li&gt;
  &lt;li class="nav-item"&gt;
    &lt;a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact"
       role="tab" aria-controls="pills-contact" aria-selected="false"&gt;Contact&lt;/a&gt;
  &lt;/li&gt;
&lt;/ul&gt;
&lt;div class="tab-content"&gt;
  &lt;div class="tab-pane fade show active" id="pills-home" role="tabpanel"
       aria-labelledby="pills-home-tab"&gt; Lorem ipsum dolor sit amet, consectetur adipisicing elit. Harum, soluta.
  &lt;/div&gt;
  &lt;div class="tab-pane fade" id="pills-profile" role="tabpanel"
       aria-labelledby="pills-profile-tab"&gt; Lorem ipsum dolor sit amet, consectetur adipisicing elit. Harum, soluta.
  &lt;/div&gt;
  &lt;div class="tab-pane fade" id="pills-contact" role="tabpanel"
       aria-labelledby="pills-contact-tab"&gt; Lorem ipsum dolor sit amet, consectetur adipisicing elit. Harum, soluta.
  &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Vertical Pill tab</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="nav flex-column nav-pills" role="tablist"
                                     aria-orientation="vertical">
                                    <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill"
                                       href="#v-pills-home" role="tab" aria-controls="v-pills-home"
                                       aria-selected="true">Home</a>
                                    <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill"
                                       href="#v-pills-profile" role="tab" aria-controls="v-pills-profile"
                                       aria-selected="false">Profile</a>
                                    <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill"
                                       href="#v-pills-messages" role="tab" aria-controls="v-pills-messages"
                                       aria-selected="false">Messages</a>
                                    <a class="nav-link" id="v-pills-settings-tab" data-toggle="pill"
                                       href="#v-pills-settings" role="tab" aria-controls="v-pills-settings"
                                       aria-selected="false">Settings</a>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="tab-content">
                                    <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                                         aria-labelledby="v-pills-home-tab"> Lorem ipsum dolor sit amet, consectetur
                                        adipisicing elit. Deserunt, totam?
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                                         aria-labelledby="v-pills-profile-tab"> Lorem ipsum dolor sit amet,
                                        consectetur adipisicing elit. Qui, quo!
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
                                         aria-labelledby="v-pills-messages-tab"> Lorem ipsum dolor sit amet,
                                        consectetur adipisicing elit. Atque, fugit.
                                    </div>
                                    <div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
                                         aria-labelledby="v-pills-settings-tab"> Lorem ipsum dolor sit amet,
                                        consectetur adipisicing elit. Deleniti, incidunt?
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="row"&gt;
  &lt;div class="col-md-3"&gt;
    &lt;div class="nav flex-column nav-pills" role="tablist"
         aria-orientation="vertical"&gt;
      &lt;a class="nav-link active" id="v-pills-home-tab" data-toggle="pill"
         href="#v-pills-home" role="tab" aria-controls="v-pills-home"
         aria-selected="true"&gt;Home&lt;/a&gt;
      &lt;a class="nav-link" id="v-pills-profile-tab" data-toggle="pill"
         href="#v-pills-profile" role="tab" aria-controls="v-pills-profile"
         aria-selected="false"&gt;Profile&lt;/a&gt;
      &lt;a class="nav-link" id="v-pills-messages-tab" data-toggle="pill"
         href="#v-pills-messages" role="tab" aria-controls="v-pills-messages"
         aria-selected="false"&gt;Messages&lt;/a&gt;
      &lt;a class="nav-link" id="v-pills-settings-tab" data-toggle="pill"
         href="#v-pills-settings" role="tab" aria-controls="v-pills-settings"
         aria-selected="false"&gt;Settings&lt;/a&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="col-md-9"&gt;
    &lt;div class="tab-content"&gt;
      &lt;div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
           aria-labelledby="v-pills-home-tab"&gt; Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deserunt, totam?
      &lt;/div&gt;
      &lt;div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
           aria-labelledby="v-pills-profile-tab"&gt; Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, quo!
      &lt;/div&gt;
      &lt;div class="tab-pane fade" id="v-pills-messages" role="tabpanel"
           aria-labelledby="v-pills-messages-tab"&gt; Lorem ipsum dolor sit amet, consectetur adipisicing elit. Atque, fugit.
      &lt;/div&gt;
      &lt;div class="tab-pane fade" id="v-pills-settings" role="tabpanel"
           aria-labelledby="v-pills-settings-tab"&gt; Lorem ipsum dolor sit amet, consectetur adipisicing elit. Deleniti, incidunt?
      &lt;/div&gt;
    &lt;/div&gt;
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
