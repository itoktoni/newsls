@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Form Validation</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Forms</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Form Validation</li>
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
                        <h6 class="card-title">Valid Feedback</h6>
                        <form class="needs-validation" novalidate="">
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustom01">First name</label>
                                    <input type="text" class="form-control" id="validationCustom01"
                                           placeholder="First name"
                                           value="Mark" required="">
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustom02">Last name</label>
                                    <input type="text" class="form-control" id="validationCustom02"
                                           placeholder="Last name"
                                           value="Otto" required="">
                                    <div class="valid-feedback">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="validationCustomUsername">Username</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                                        </div>
                                        <input type="text" class="form-control" id="validationCustomUsername"
                                               placeholder="Username" aria-describedby="inputGroupPrepend"
                                               required="">
                                        <div class="invalid-feedback">
                                            Please choose a username.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="validationCustom03">City</label>
                                    <input type="text" class="form-control" id="validationCustom03"
                                           placeholder="City"
                                           required="">
                                    <div class="invalid-feedback">
                                        Please provide a valid city.
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="validationCustom04">State</label>
                                    <input type="text" class="form-control" id="validationCustom04"
                                           placeholder="State"
                                           required="">
                                    <div class="invalid-feedback">
                                        Please provide a valid state.
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="validationCustom05">Zip</label>
                                    <input type="text" class="form-control" id="validationCustom05"
                                           placeholder="Zip"
                                           required="">
                                    <div class="invalid-feedback">
                                        Please provide a valid zip.
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck"
                                           required="">
                                    <label class="form-check-label" for="invalidCheck">
                                        Agree to terms and conditions
                                    </label>
                                    <div class="invalid-feedback">
                                        You must agree before submitting.
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit form</button>
                        </form>

                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form class="needs-validation" novalidate&gt;
  &lt;div class="form-row"&gt;
    &lt;div class="col-md-4 mb-3"&gt;
      &lt;label for="validationCustom01"&gt;First name&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationCustom01" placeholder="First name" value="Mark" required&gt;
      &lt;div class="valid-feedback"&gt;
        Looks good!
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-4 mb-3"&gt;
      &lt;label for="validationCustom02"&gt;Last name&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationCustom02" placeholder="Last name" value="Otto" required&gt;
      &lt;div class="valid-feedback"&gt;
        Looks good!
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-4 mb-3"&gt;
      &lt;label for="validationCustomUsername"&gt;Username&lt;/label&gt;
      &lt;div class="input-group"&gt;
        &lt;div class="input-group-prepend"&gt;
          &lt;span class="input-group-text" id="inputGroupPrepend"&gt;@&lt;/span&gt;
        &lt;/div&gt;
        &lt;input type="text" class="form-control" id="validationCustomUsername" placeholder="Username" aria-describedby="inputGroupPrepend" required&gt;
        &lt;div class="invalid-feedback"&gt;
          Please choose a username.
        &lt;/div&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-row"&gt;
    &lt;div class="col-md-6 mb-3"&gt;
      &lt;label for="validationCustom03"&gt;City&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationCustom03" placeholder="City" required&gt;
      &lt;div class="invalid-feedback"&gt;
        Please provide a valid city.
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-3 mb-3"&gt;
      &lt;label for="validationCustom04"&gt;State&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationCustom04" placeholder="State" required&gt;
      &lt;div class="invalid-feedback"&gt;
        Please provide a valid state.
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-3 mb-3"&gt;
      &lt;label for="validationCustom05"&gt;Zip&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationCustom05" placeholder="Zip" required&gt;
      &lt;div class="invalid-feedback"&gt;
        Please provide a valid zip.
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group"&gt;
    &lt;div class="form-check"&gt;
      &lt;input class="form-check-input" type="checkbox" value="" id="invalidCheck" required&gt;
      &lt;label class="form-check-label" for="invalidCheck"&gt;
        Agree to terms and conditions
      &lt;/label&gt;
      &lt;div class="invalid-feedback"&gt;
        You must agree before submitting.
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;button class="btn btn-primary" type="submit"&gt;Submit form&lt;/button&gt;
&lt;/form&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">// Example starter JavaScript for disabling form submissions if there are invalid fields
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Tooltip</h6>
                        <P>If your form layout allows it, you can swap the <code>.{valid|invalid}-feedback</code>
                            classes for <code>.{valid|invalid}-tooltip</code> classes to display validation feedback
                            in a styled tooltip. Be sure to have a parent with <code>position: relative</code> on it
                            for tooltip positioning. In the example below, our column classes have this already, but
                            your project may require an alternative setup.</P>
                        <form class="needs-validation" novalidate>
                            <div class="form-row">
                                <div class="col-md-4 mb-3">
                                    <label for="validationTooltip01">First name</label>
                                    <input type="text" class="form-control" id="validationTooltip01"
                                           placeholder="First name"
                                           value="Mark" required>
                                    <div class="valid-tooltip">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="validationTooltip02">Last name</label>
                                    <input type="text" class="form-control" id="validationTooltip02"
                                           placeholder="Last name"
                                           value="Otto" required>
                                    <div class="valid-tooltip">
                                        Looks good!
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="validationTooltipUsername">Username</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                                <span class="input-group-text"
                                                      id="validationTooltipUsernamePrepend">@</span>
                                        </div>
                                        <input type="text" class="form-control" id="validationTooltipUsername"
                                               placeholder="Username"
                                               aria-describedby="validationTooltipUsernamePrepend"
                                               required>
                                        <div class="invalid-tooltip">
                                            Please choose a unique and valid username.
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label for="validationTooltip03">City</label>
                                    <input type="text" class="form-control" id="validationTooltip03"
                                           placeholder="City"
                                           required>
                                    <div class="invalid-tooltip">
                                        Please provide a valid city.
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="validationTooltip04">State</label>
                                    <input type="text" class="form-control" id="validationTooltip04"
                                           placeholder="State"
                                           required>
                                    <div class="invalid-tooltip">
                                        Please provide a valid state.
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="validationTooltip05">Zip</label>
                                    <input type="text" class="form-control" id="validationTooltip05"
                                           placeholder="Zip" required>
                                    <div class="invalid-tooltip">
                                        Please provide a valid zip.
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit">Submit form</button>
                        </form>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form class="needs-validation" novalidate&gt;
  &lt;div class="form-row"&gt;
    &lt;div class="col-md-4 mb-3"&gt;
      &lt;label for="validationTooltip01"&gt;First name&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationTooltip01" placeholder="First name" value="Mark" required&gt;
      &lt;div class="valid-tooltip"&gt;
        Looks good!
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-4 mb-3"&gt;
      &lt;label for="validationTooltip02"&gt;Last name&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationTooltip02" placeholder="Last name" value="Otto" required&gt;
      &lt;div class="valid-tooltip"&gt;
        Looks good!
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-4 mb-3"&gt;
      &lt;label for="validationTooltipUsername"&gt;Username&lt;/label&gt;
      &lt;div class="input-group"&gt;
        &lt;div class="input-group-prepend"&gt;
          &lt;span class="input-group-text" id="validationTooltipUsernamePrepend"&gt;@&lt;/span&gt;
        &lt;/div&gt;
        &lt;input type="text" class="form-control" id="validationTooltipUsername" placeholder="Username" aria-describedby="validationTooltipUsernamePrepend" required&gt;
        &lt;div class="invalid-tooltip"&gt;
          Please choose a unique and valid username.
        &lt;/div&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-row"&gt;
    &lt;div class="col-md-6 mb-3"&gt;
      &lt;label for="validationTooltip03"&gt;City&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationTooltip03" placeholder="City" required&gt;
      &lt;div class="invalid-tooltip"&gt;
        Please provide a valid city.
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-3 mb-3"&gt;
      &lt;label for="validationTooltip04"&gt;State&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationTooltip04" placeholder="State" required&gt;
      &lt;div class="invalid-tooltip"&gt;
        Please provide a valid state.
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;div class="col-md-3 mb-3"&gt;
      &lt;label for="validationTooltip05"&gt;Zip&lt;/label&gt;
      &lt;input type="text" class="form-control" id="validationTooltip05" placeholder="Zip" required&gt;
      &lt;div class="invalid-tooltip"&gt;
        Please provide a valid zip.
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;button class="btn btn-primary" type="submit"&gt;Submit form&lt;/button&gt;
&lt;/form&gt;</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')
    <script>
        //  Form Validation
        window.addEventListener('load', function () {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName('needs-validation');
            // Loop over them and prevent submission
            var validation = Array.prototype.filter.call(forms, function (form) {
                form.addEventListener('submit', function (event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    </script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
