@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Avatar</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Components</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Avatar</li>
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
                        <h6 class="card-title">Default</h6>
                        <figure class="avatar">
                            <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;figure class="avatar"&gt;
  &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Sizes</h6>
                        <figure class="avatar avatar-xl">
                            <img src="{{ url('assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <figure class="avatar avatar-lg">
                            <img src="{{ url('assets/media/image/user/man_avatar2.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <figure class="avatar">
                            <img src="{{ url('assets/media/image/user/women_avatar1.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <figure class="avatar avatar-sm">
                            <img src="{{ url('assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;figure class="avatar avatar-xl"&gt;
  &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-lg"&gt;
  &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;
&lt;figure class="avatar"&gt;
  &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-sm"&gt;
  &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Avatar text placeholders</h6>
                        <figure class="avatar">
                            <span class="avatar-title rounded-circle">A</span>
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                        <pre><code class="language-html">&lt;figure class="avatar"&gt;
  &lt;span class="avatar-title rounded-circle"&gt;A&lt;/span&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Avatar text sizes</h6>
                        <figure class="avatar avatar-xl">
                            <span class="avatar-title rounded-circle">A</span>
                        </figure>
                        <figure class="avatar avatar-lg">
                            <span class="avatar-title rounded-circle">B</span>
                        </figure>
                        <figure class="avatar">
                            <span class="avatar-title rounded-circle">C</span>
                        </figure>
                        <figure class="avatar avatar-sm">
                            <span class="avatar-title rounded-circle">D</span>
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;figure class="avatar avatar-xl"&gt;
  &lt;span class="avatar-title rounded-circle"&gt;A&lt;/span&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-lg"&gt;
  &lt;span class="avatar-title rounded-circle"&gt;B&lt;/span&gt;
&lt;/figure&gt;
&lt;figure class="avatar"&gt;
  &lt;span class="avatar-title rounded-circle"&gt;C&lt;/span&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-sm"&gt;
  &lt;span class="avatar-title rounded-circle"&gt;D&lt;/span&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Avatar with link</h6>
                        <figure class="avatar">
                            <a href="#">
                                <span class="avatar-title rounded-circle">A</span>
                            </a>
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;figure class="avatar"&gt;
  &lt;a href="#"&gt;
    &lt;span class="avatar-title rounded-circle"&gt;A&lt;/span&gt;
  &lt;/a&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Colorful avatars</h6>
                        <figure class="avatar avatar-xl">
                            <span class="avatar-title bg-primary rounded-circle">A</span>
                        </figure>
                        <figure class="avatar avatar-lg">
                            <span class="avatar-title bg-info rounded-circle">B</span>
                        </figure>
                        <figure class="avatar">
                            <span class="avatar-title bg-success rounded-circle">C</span>
                        </figure>
                        <figure class="avatar avatar-sm">
                            <span class="avatar-title bg-danger rounded-circle">D</span>
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;figure class="avatar avatar-xl"&gt;
  &lt;span class="avatar-title bg-primary rounded-circle"&gt;A&lt;/span&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-lg"&gt;
  &lt;span class="avatar-title bg-info rounded-circle"&gt;B&lt;/span&gt;
&lt;/figure&gt;
&lt;figure class="avatar"&gt;
  &lt;span class="avatar-title bg-success rounded-circle"&gt;C&lt;/span&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-sm"&gt;
  &lt;span class="avatar-title bg-danger rounded-circle"&gt;D&lt;/span&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Avatar states</h6>
                        <figure class="avatar avatar-xl avatar-state-warning">
                            <img src="{{ url('assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <figure class="avatar avatar-lg avatar-state-success">
                            <img src="{{ url('assets/media/image/user/women_avatar1.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <figure class="avatar avatar-state-danger">
                            <img src="{{ url('assets/media/image/user/man_avatar3.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <figure class="avatar avatar-sm avatar-state-secondary">
                            <img src="{{ url('assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;figure class="avatar avatar-xl avatar-state-warning"&gt;
    &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-lg avatar-state-success"&gt;
    &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-state-danger"&gt;
    &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;
&lt;figure class="avatar avatar-sm avatar-state-secondary"&gt;
    &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Avatar shapes</h6>
                        <figure class="avatar">
                            <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}" alt="image">
                        </figure>
                        <figure class="avatar">
                            <img src="{{ url('assets/media/image/user/man_avatar2.jpg') }}" class="rounded" alt="image">
                        </figure>
                        <figure class="avatar">
                            <img src="{{ url('assets/media/image/user/women_avatar5.jpg') }}" class="rounded-circle" alt="image">
                        </figure>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;figure class="avatar"&gt;
    &lt;img src="avatar.jpg" alt="avatar"&gt;
&lt;/figure&gt;

&lt;figure class="avatar"&gt;
    &lt;img src="avatar.jpg" class="rounded" alt="avatar"&gt;
&lt;/figure&gt;

&lt;figure class="avatar"&gt;
    &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
&lt;/figure&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Avatar group</h6>
                        <div class="avatar-group">
                            <figure class="avatar">
                                <span class="avatar-title bg-success rounded-circle">E</span>
                            </figure>
                            <figure class="avatar">
                                <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}" class="rounded-circle"
                                     alt="image">
                            </figure>
                            <figure class="avatar">
                                <span class="avatar-title bg-danger rounded-circle">S</span>
                            </figure>
                            <figure class="avatar">
                                <img src="{{ url('assets/media/image/user/women_avatar3.jpg') }}" class="rounded-circle"
                                     alt="image">
                            </figure>
                            <figure class="avatar">
                                <span class="avatar-title bg-primary rounded-circle">C</span>
                            </figure>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="avatar-group"&gt;
    &lt;figure class="avatar"&gt;
        &lt;span class="avatar-title bg-success rounded-circle"&gt;E&lt;/span&gt;
    &lt;/figure&gt;
    &lt;figure class="avatar"&gt;
        &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
    &lt;/figure&gt;
    &lt;figure class="avatar"&gt;
        &lt;span class="avatar-title bg-danger rounded-circle"&gt;S&lt;/span&gt;
    &lt;/figure&gt;
    &lt;figure class="avatar"&gt;
        &lt;img src="avatar.jpg" class="rounded-circle" alt="avatar"&gt;
    &lt;/figure&gt;
    &lt;figure class="avatar"&gt;
        &lt;span class="avatar-title bg-primary rounded-circle"&gt;C&lt;/span&gt;
    &lt;/figure&gt;
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
