@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>List Group</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">List Group</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Basic</h6>
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group">
                            <li class="list-group-item">Cras justo odio</li>
                            <li class="list-group-item">Dapibus ac facilisis in</li>
                            <li class="list-group-item">Morbi leo risus</li>
                            <li class="list-group-item">Porta ac consectetur ac</li>
                            <li class="list-group-item">Vestibulum at eros</li>
                        </ul>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;ul class="list-group"&gt;
  &lt;li class="list-group-item"&gt;Cras justo odio&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Dapibus ac facilisis in&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Morbi leo risus&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Porta ac consectetur ac&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Vestibulum at eros&lt;/li&gt;
&lt;/ul&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Active items</h6>
                <p>Add the <code>.active</code> class to use.</p>
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group">
                            <li class="list-group-item">Cras justo odio</li>
                            <li class="list-group-item active">Dapibus ac facilisis in</li>
                            <li class="list-group-item">Morbi leo risus</li>
                            <li class="list-group-item">Porta ac consectetur ac</li>
                            <li class="list-group-item">Vestibulum at eros</li>
                        </ul>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;ul class="list-group"&gt;
  &lt;li class="list-group-item"&gt;Cras justo odio&lt;/li&gt;
  &lt;li class="list-group-item active"&gt;Dapibus ac facilisis in&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Morbi leo risus&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Porta ac consectetur ac&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Vestibulum at eros&lt;/li&gt;
&lt;/ul&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Disabled items</h6>
                <p>Add the <code>.disabled</code> class to use.</p>
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group">
                            <li class="list-group-item disabled" aria-disabled="true">Cras justo odio</li>
                            <li class="list-group-item">Dapibus ac facilisis in</li>
                            <li class="list-group-item">Morbi leo risus</li>
                            <li class="list-group-item">Porta ac consectetur ac</li>
                            <li class="list-group-item">Vestibulum at eros</li>
                        </ul>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;ul class="list-group"&gt;
  &lt;li class="list-group-item disabled" aria-disabled="true"&gt;Cras justo odio&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Dapibus ac facilisis in&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Morbi leo risus&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Porta ac consectetur ac&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Vestibulum at eros&lt;/li&gt;
&lt;/ul&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Links and buttons</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action">
                                Cras justo odio
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">Dapibus ac facilisis in</a>
                            <a href="#" class="list-group-item list-group-item-action">Morbi leo risus</a>
                            <a href="#" class="list-group-item list-group-item-action">Porta ac consectetur ac</a>
                            <a href="#" class="list-group-item list-group-item-action">Vestibulum at eros</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="list-group">
                            <button type="button" class="list-group-item list-group-item-action">
                                Cras justo odio
                            </button>
                            <button type="button" class="list-group-item list-group-item-action">Dapibus ac
                                facilisis
                                in
                            </button>
                            <button type="button" class="list-group-item list-group-item-action">Morbi leo risus
                            </button>
                            <button type="button" class="list-group-item list-group-item-action">Porta ac
                                consectetur
                                ac
                            </button>
                            <button type="button" class="list-group-item list-group-item-action">Vestibulum at
                                eros
                            </button>
                        </div>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;!-- Link --&gt;
&lt;div class="list-group"&gt;
  &lt;a href="#" class="list-group-item list-group-item-action"&gt;...&lt;/a&gt;
  &lt;a href="#" class="list-group-item list-group-item-action"&gt;...&lt;/a&gt;
  &lt;a href="#" class="list-group-item list-group-item-action"&gt;...&lt;/a&gt;
  &lt;a href="#" class="list-group-item list-group-item-action"&gt;...&lt;/a&gt;
  &lt;a href="#" class="list-group-item list-group-item-action"&gt;...&lt;/a&gt;
&lt;/div&gt;

&lt;!-- Button --&gt;
&lt;div class="list-group"&gt;
  &lt;button type="button" class="list-group-item list-group-item-action"&gt;...&lt;/button&gt;
  &lt;button type="button" class="list-group-item list-group-item-action"&gt;...&lt;/button&gt;
  &lt;button type="button" class="list-group-item list-group-item-action"&gt;...&lt;/button&gt;
  &lt;button type="button" class="list-group-item list-group-item-action"&gt;...&lt;/button&gt;
  &lt;button type="button" class="list-group-item list-group-item-action"&gt;...&lt;/button&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Flush</h6>
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">Cras justo odio</li>
                            <li class="list-group-item">Dapibus ac facilisis in</li>
                            <li class="list-group-item">Morbi leo risus</li>
                            <li class="list-group-item">Porta ac consectetur ac</li>
                            <li class="list-group-item">Vestibulum at eros</li>
                        </ul>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;ul class="list-group list-group-flush"&gt;
  &lt;li class="list-group-item"&gt;Cras justo odio&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Dapibus ac facilisis in&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Morbi leo risus&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Porta ac consectetur ac&lt;/li&gt;
  &lt;li class="list-group-item"&gt;Vestibulum at eros&lt;/li&gt;
&lt;/ul&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Contextual classes</h6>
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-primary">A simple primary list group item
                            </li>
                            <li class="list-group-item list-group-item-secondary">A simple secondary list group item
                            </li>
                            <li class="list-group-item list-group-item-success">A simple success list group item
                            </li>
                            <li class="list-group-item list-group-item-danger">A simple danger list group item</li>
                            <li class="list-group-item list-group-item-warning">A simple warning list group item
                            </li>
                            <li class="list-group-item list-group-item-info">A simple info list group item</li>
                            <li class="list-group-item list-group-item-light">A simple light list group item</li>
                            <li class="list-group-item list-group-item-dark">A simple dark list group item</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <ul class="list-group">
                            <li class="list-group-item list-group-item-primary-bright">A simple primary list group
                                item
                            </li>
                            <li class="list-group-item list-group-item-secondary-bright">A simple secondary list
                                group
                                item
                            </li>
                            <li class="list-group-item list-group-item-success-bright">A simple success list group
                                item
                            </li>
                            <li class="list-group-item list-group-item-danger-bright">A simple danger list group
                                item
                            </li>
                            <li class="list-group-item list-group-item-warning-bright">A simple warning list group
                                item
                            </li>
                            <li class="list-group-item list-group-item-info-bright">A simple info list group item
                            </li>
                            <li class="list-group-item list-group-item-dark-bright">A simple dark list group item
                            </li>
                        </ul>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;ul class="list-group"&gt;
  &lt;li class="list-group-item list-group-item-primary"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-secondary"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-success"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-danger"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-warning"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-info"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-light"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-dark"&gt;...&lt;/li&gt;
&lt;/ul&gt;

&lt;!-- Light --&gt;
&lt;ul class="list-group"&gt;
  &lt;li class="list-group-item list-group-item-primary-bright"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-secondary-bright"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-success-bright"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-danger-bright"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-warning-bright"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-info-bright"&gt;...&lt;/li&gt;
  &lt;li class="list-group-item list-group-item-dark-bright"&gt;...&lt;/li&gt;
&lt;/ul&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">With badges</h6>
                <div class="row">
                    <div class="col-md-4">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Cras justo odio
                                <span class="badge badge-primary badge-pill">14</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Dapibus ac facilisis in
                                <span class="badge badge-primary badge-pill">2</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Morbi leo risus
                                <span class="badge badge-primary badge-pill">1</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;ul class="list-group"&gt;
  &lt;li class="list-group-item d-flex justify-content-between align-items-center"&gt;
    Cras justo odio
    &lt;span class="badge badge-primary badge-pill"&gt;14&lt;/span&gt;
  &lt;/li&gt;
  &lt;li class="list-group-item d-flex justify-content-between align-items-center"&gt;
    Dapibus ac facilisis in
    &lt;span class="badge badge-primary badge-pill"&gt;2&lt;/span&gt;
  &lt;/li&gt;
  &lt;li class="list-group-item d-flex justify-content-between align-items-center"&gt;
    Morbi leo risus
    &lt;span class="badge badge-primary badge-pill"&gt;1&lt;/span&gt;
  &lt;/li&gt;
&lt;/ul&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">Custom content</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="list-group">
                            <a href="#" class="list-group-item list-group-item-action active">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">List group item heading</h5>
                                    <small>3 days ago</small>
                                </div>
                                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam
                                    eget
                                    risus varius blandit.</p>
                                <small>Donec id elit non mi porta.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">List group item heading</h5>
                                    <small class="text-muted">3 days ago</small>
                                </div>
                                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam
                                    eget
                                    risus varius blandit.</p>
                                <small class="text-muted">Donec id elit non mi porta.</small>
                            </a>
                            <a href="#" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">List group item heading</h5>
                                    <small class="text-muted">3 days ago</small>
                                </div>
                                <p class="mb-1">Donec id elit non mi porta gravida at eget metus. Maecenas sed diam
                                    eget
                                    risus varius blandit.</p>
                                <small class="text-muted">Donec id elit non mi porta.</small>
                            </a>
                        </div>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="list-group"&gt;
  &lt;a href="#" class="list-group-item list-group-item-action active"&gt;
    &lt;div class="d-flex w-100 justify-content-between"&gt;
      &lt;h5 class="mb-1"&gt;List group item heading&lt;/h5&gt;
      &lt;small&gt;3 days ago&lt;/small&gt;
    &lt;/div&gt;
    &lt;p class="mb-1"&gt;Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.&lt;/p&gt;
    &lt;small&gt;Donec id elit non mi porta.&lt;/small&gt;
  &lt;/a&gt;
  &lt;a href="#" class="list-group-item list-group-item-action"&gt;
    &lt;div class="d-flex w-100 justify-content-between"&gt;
      &lt;h5 class="mb-1"&gt;List group item heading&lt;/h5&gt;
      &lt;small class="text-muted"&gt;3 days ago&lt;/small&gt;
    &lt;/div&gt;
    &lt;p class="mb-1"&gt;Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.&lt;/p&gt;
    &lt;small class="text-muted"&gt;Donec id elit non mi porta.&lt;/small&gt;
  &lt;/a&gt;
  &lt;a href="#" class="list-group-item list-group-item-action"&gt;
    &lt;div class="d-flex w-100 justify-content-between"&gt;
      &lt;h5 class="mb-1"&gt;List group item heading&lt;/h5&gt;
      &lt;small class="text-muted"&gt;3 days ago&lt;/small&gt;
    &lt;/div&gt;
    &lt;p class="mb-1"&gt;Donec id elit non mi porta gravida at eget metus. Maecenas sed diam eget risus varius blandit.&lt;/p&gt;
    &lt;small class="text-muted"&gt;Donec id elit non mi porta.&lt;/small&gt;
  &lt;/a&gt;
&lt;/div&gt;</code></pre>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="card-title">JavaScript behavior</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="list-group" id="list-tab" role="tablist">
                            <a class="list-group-item list-group-item-action active" id="list-home-list"
                               data-toggle="list" href="#list-home" role="tab">Home</a>
                            <a class="list-group-item list-group-item-action" id="list-profile-list"
                               data-toggle="list"
                               href="#list-profile" role="tab">Profile</a>
                            <a class="list-group-item list-group-item-action" id="list-messages-list"
                               data-toggle="list"
                               href="#list-messages" role="tab">Messages</a>
                            <a class="list-group-item list-group-item-action" id="list-settings-list"
                               data-toggle="list"
                               href="#list-settings" role="tab">Settings</a>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="list-home" role="tabpanel"
                                 aria-labelledby="list-home-list">Lorem ipsum dolor sit amet, consectetur
                                adipisicing
                                elit. Minima, veritatis.
                            </div>
                            <div class="tab-pane fade" id="list-profile" role="tabpanel"
                                 aria-labelledby="list-profile-list">Lorem ipsum dolor sit amet, consectetur
                                adipisicing
                                elit. Deserunt dolor nam repudiandae! Amet consequatur exercitationem fugiat harum
                                laudantium obcaecati possimus.
                            </div>
                            <div class="tab-pane fade" id="list-messages" role="tabpanel"
                                 aria-labelledby="list-messages-list">Lorem ipsum dolor sit amet.
                            </div>
                            <div class="tab-pane fade" id="list-settings" role="tabpanel"
                                 aria-labelledby="list-settings-list">Lorem ipsum dolor sit amet, consectetur
                                adipisicing elit. A cum debitis et id incidunt itaque labore, sapiente tempora!
                                Aliquam
                                aliquid amet aspernatur enim eveniet explicabo ipsam ipsum qui quos ut.
                            </div>
                        </div>
                    </div>
                </div>
                <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;div class="row"&gt;
  &lt;div class="col-md-4"&gt;
    &lt;div class="list-group" id="list-tab" role="tablist"&gt;
      &lt;a class="list-group-item list-group-item-action active" id="list-home-list" data-toggle="list" href="#list-home" role="tab"&gt;Home&lt;/a&gt;
      &lt;a class="list-group-item list-group-item-action" id="list-profile-list" data-toggle="list" href="#list-profile" role="tab"&gt;Profile&lt;/a&gt;
      &lt;a class="list-group-item list-group-item-action" id="list-messages-list" data-toggle="list" href="#list-messages" role="tab" &gt;Messages&lt;/a&gt;
      &lt;a class="list-group-item list-group-item-action" id="list-settings-list" data-toggle="list" href="#list-settings" role="tab" &gt;Settings&lt;/a&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="col-md-8"&gt;
    &lt;div class="tab-content" id="nav-tabContent"&gt;
      &lt;div class="tab-pane fade show active" id="list-home" role="tabpanel" aria-labelledby="list-home-list"&gt;...&lt;/div&gt;
      &lt;div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list"&gt;...&lt;/div&gt;
      &lt;div class="tab-pane fade" id="list-messages" role="tabpanel" aria-labelledby="list-messages-list"&gt;...&lt;/div&gt;
      &lt;div class="tab-pane fade" id="list-settings" role="tabpanel" aria-labelledby="list-settings-list"&gt;...&lt;/div&gt;
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
