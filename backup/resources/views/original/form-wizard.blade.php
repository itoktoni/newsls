@extends('layouts.app')

@section('head')
    <!-- Form wizard -->
    <link rel="stylesheet" href="{{ url('vendors/form-wizard/jquery.steps.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Form Wizard</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Forms</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Form Wizard</li>
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
                        <p class="lead">
                            An all-in-one wizard plugin that is extremely flexible, compact and feature-rich. <a
                                href="http://www.jquery-steps.com/Examples" class="link-1" target="_blank">Demo
                                page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Css --&gt;
&lt;link rel="stylesheet" href="vendors/form-wizard/jquery.steps.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/form-wizard/jquery.steps.min.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Basic Content Wizard</h6>
                        <p class="text-muted">Below is an example of a basic horizontal form wizard.</p>
                        <div id="wizard1">
                            <h3>Personal Information</h3>
                            <section class="card card-body border mb-0">
                                <h5>Personal Information</h5>
                                <p>Try the keyboard navigation by clicking arrow left or right!</p>
                            </section>
                            <h3>Billing Information</h3>
                            <section class="card card-body border mb-0">
                                <h5>Billing Information</h5>
                                <p>Wonderful transition effects.</p>
                            </section>
                            <h3>Payment Details</h3>
                            <section class="card card-body border mb-0">
                                <h5>Payment Details</h5>
                                <p>The next and previous buttons help you to navigate through your content.</p>
                            </section>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div id="wizard-example"&gt;
  &lt;h3&gt;Personal Information&lt;/h3&gt;
  &lt;section class="card card-body border mb-0"&gt;
    &lt;h5&gt;Personal Information&lt;/h5&gt;
    &lt;p&gt;Try the keyboard navigation by clicking arrow left or right!&lt;/p&gt;
  &lt;/section&gt;
  &lt;h3&gt;Billing Information&lt;/h3&gt;
  &lt;section class="card card-body border mb-0"&gt;
    &lt;h5&gt;Billing Information&lt;/h5&gt;
    &lt;p&gt;Wonderful transition effects.&lt;/p&gt;
  &lt;/section&gt;
  &lt;h3&gt;Payment Details&lt;/h3&gt;
  &lt;section class="card card-body border mb-0"&gt;
    &lt;h5&gt;Payment Details&lt;/h5&gt;
    &lt;p&gt;The next and previous buttons help you to navigate through your content.&lt;/p&gt;
  &lt;/section&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('#wizard-example').steps({
    headerTag: 'h3',
    bodyTag: 'section',
    autoFocus: true,
    titleTemplate: '&lt;span class="wizard-index"&gt;#index#&lt;/span&gt; #title#'
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title"> Basic Form Wizard with Validation</h6>
                        <div id="wizard2">
                            <h3>Personal Information</h3>
                            <section class="card card-body border mb-0">
                                <h5>Personal Information</h5>
                                <p>Try the keyboard navigation by clicking arrow left or right!</p>
                                <form id="form1">
                                    <div class="form-group wd-xs-300">
                                        <label>First name</label>
                                        <input type="text" class="form-control" placeholder="First name" required>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div><!-- form-group -->
                                    <div class="form-group wd-xs-300">
                                        <label>Last name</label>
                                        <input type="text" class="form-control" name="lastname"
                                               placeholder="Enter lastname"
                                               required>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div><!-- form-group -->
                                </form>
                            </section>
                            <h3>Billing Information</h3>
                            <section class="card card-body border mb-0">
                                <h5>Billing Information</h5>
                                <p>Wonderful transition effects.</p>
                                <form id="form2">
                                    <div class="form-group wd-xs-300">
                                        <label class="form-control-label">Email: <span
                                                class="tx-danger">*</span></label>
                                        <input id="email" class="form-control" name="email"
                                               placeholder="Enter email address"
                                               type="email" required>
                                        <div class="valid-feedback">
                                            Looks good!
                                        </div>
                                    </div><!-- form-group -->
                                </form>
                            </section>
                            <h3>Payment Details</h3>
                            <section class="card card-body border mb-0">
                                <h5>Payment Details</h5>
                                <p>The next and previous buttons help you to navigate through your content.</p>
                            </section>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div id="wizard-example"&gt;
    &lt;h3&gt;Personal Information&lt;/h3&gt;
    &lt;section class="card card-body border mb-0"&gt;
        &lt;h5&gt;Personal Information&lt;/h5&gt;
        &lt;p&gt;Try the keyboard navigation by clicking arrow left or right!&lt;/p&gt;
        &lt;form id="form1"&gt;
            &lt;div class="form-group wd-xs-300"&gt;
                &lt;label&gt;First name&lt;/label&gt;
                &lt;input type="text" class="form-control" placeholder="First name" required&gt;
                &lt;div class="valid-feedback"&gt;
                    Looks good!
                &lt;/div&gt;
            &lt;/div&gt;
            &lt;div class="form-group wd-xs-300"&gt;
                &lt;label&gt;Last name&lt;/label&gt;
                &lt;input type="text" class="form-control" name="lastname" placeholder="Enter lastname"
                       required&gt;
                &lt;div class="valid-feedback"&gt;
                    Looks good!
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/form&gt;
    &lt;/section&gt;
    &lt;h3&gt;Billing Information&lt;/h3&gt;
    &lt;section class="card card-body border mb-0"&gt;
        &lt;h5&gt;Billing Information&lt;/h5&gt;
        &lt;p&gt;Wonderful transition effects.&lt;/p&gt;
        &lt;form id="form2"&gt;
            &lt;div class="form-group wd-xs-300"&gt;
                &lt;label class="form-control-label"&gt;Email: &lt;span class="tx-danger"&gt;*&lt;/span&gt;&lt;/label&gt;
                &lt;input id="email" class="form-control" name="email" placeholder="Enter email address"
                       type="email" required&gt;
                &lt;div class="valid-feedback"&gt;
                    Looks good!
                &lt;/div&gt;
            &lt;/div&gt;
        &lt;/form&gt;
    &lt;/section&gt;
    &lt;h3&gt;Payment Details&lt;/h3&gt;
    &lt;section class="card card-body border mb-0"&gt;
        &lt;h5&gt;Payment Details&lt;/h5&gt;
        &lt;p&gt;The next and previous buttons help you to navigate through your content.&lt;/p&gt;
    &lt;/section&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('#wizard-example').steps({
    headerTag: 'h3',
    bodyTag: 'section',
    autoFocus: true,
    titleTemplate: '&lt;span class="wizard-index"&gt;#index#&lt;/span&gt; #title#',
    onStepChanging: function (event, currentIndex, newIndex) {
        if (currentIndex &lt; newIndex) {
            var form = document.getElementById('form1'),
                form2 = document.getElementById('form2');

            if (currentIndex === 0) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    form.classList.add('was-validated');
                } else {
                    return true;
                }
            } else if (currentIndex === 1) {
                if (form2.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    form2.classList.add('was-validated');
                } else {
                    return true;
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }
});</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- Form wizard -->
    <script src="{{ url('vendors/form-wizard/jquery.steps.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/form-wizard.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
