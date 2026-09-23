@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Form Layouts</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Forms</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Form Layouts</li>
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
                        <h6 class="card-title">Overview</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <form>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Email address</label>
                                        <input type="email" class="form-control" id="exampleInputEmail1"
                                               aria-describedby="emailHelp" placeholder="Enter email">
                                        <small id="emailHelp" class="form-text text-muted">We'll never share your
                                            email with
                                            anyone else.
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Password</label>
                                        <input type="password" class="form-control" id="exampleInputPassword1"
                                               placeholder="Password">
                                    </div>
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
    &lt;div class="form-group"&gt;
        &lt;label for="exampleInputEmail1"&gt;Email address&lt;/label&gt;
        &lt;input type="email" class="form-control" id="exampleInputEmail1"
               aria-describedby="emailHelp" placeholder="Enter email"&gt;
        &lt;small id="emailHelp" class="form-text text-muted"&gt;We'll never share your email with
            anyone else.
        &lt;/small&gt;
    &lt;/div&gt;
    &lt;div class="form-group"&gt;
        &lt;label for="exampleInputPassword1"&gt;Password&lt;/label&gt;
        &lt;input type="password" class="form-control" id="exampleInputPassword1"
               placeholder="Password"&gt;
    &lt;/div&gt;
    &lt;div class="form-group form-check"&gt;
        &lt;input type="checkbox" class="form-check-input" id="exampleCheck1"&gt;
        &lt;label class="form-check-label" for="exampleCheck1"&gt;Check me out&lt;/label&gt;
    &lt;/div&gt;
    &lt;button type="submit" class="btn btn-primary"&gt;Submit&lt;/button&gt;
&lt;/form&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Form Controls</h6>
                        <form>
                            <div class="form-group">
                                <label for="exampleFormControlInput1">Email address</label>
                                <input type="email" class="form-control" id="exampleFormControlInput1"
                                       placeholder="name@example.com">
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlSelect1">Example select</label>
                                <select class="form-control" id="exampleFormControlSelect1">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlSelect2">Example multiple select</label>
                                <select multiple class="form-control" id="exampleFormControlSelect2">
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option>5</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Example textarea</label>
                                <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                            </div>
                        </form>
                        <p>For file inputs, swap the <code class="highlighter-rouge">.form-control</code> for <code
                                class="highlighter-rouge">.form-control-file</code>.</p>
                        <form>
                            <div class="form-group">
                                <label for="exampleFormControlFile1">Example file input</label>
                                <input type="file" class="form-control-file" id="exampleFormControlFile1">
                            </div>
                        </form>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Input --&gt;
&lt;div class="form-group"&gt;
  &lt;label for="exampleFormControlInput1"&gt;Email address&lt;/label&gt;
  &lt;input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com"&gt;
&lt;/div&gt;

&lt;!-- Select --&gt;
&lt;div class="form-group"&gt;
  &lt;label for="exampleFormControlSelect1"&gt;Example select&lt;/label&gt;
  &lt;select class="form-control" id="exampleFormControlSelect1"&gt;
    &lt;option&gt;1&lt;/option&gt;
    &lt;option&gt;2&lt;/option&gt;
    &lt;option&gt;3&lt;/option&gt;
    &lt;option&gt;4&lt;/option&gt;
    &lt;option&gt;5&lt;/option&gt;
  &lt;/select&gt;
&lt;/div&gt;

&lt;!-- Textarea --&gt;
&lt;div class="form-group"&gt;
  &lt;label for="exampleFormControlTextarea1"&gt;Example textarea&lt;/label&gt;
  &lt;textarea class="form-control" id="exampleFormControlTextarea1" rows="3"&gt;&lt;/textarea&gt;
&lt;/div&gt;

&lt;!-- File input --&gt;
&lt;div class="form-group"&gt;
  &lt;label for="exampleFormControlFile1"&gt;Example file input&lt;/label&gt;
  &lt;input type="file" class="form-control-file" id="exampleFormControlFile1"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Sizing Input</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <input class="form-control form-control-lg" type="text"
                                           placeholder=".form-control-lg">
                                </div>
                                <div class="form-group">
                                    <input class="form-control" type="text" placeholder="Default input">
                                </div>
                                <div class="form-group">
                                    <input class="form-control form-control-sm" type="text"
                                           placeholder=".form-control-sm">
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;input class="form-control form-control-lg" type="text" placeholder=".form-control-lg"&gt;
&lt;input class="form-control" type="text" placeholder="Default input"&gt;
&lt;input class="form-control form-control-sm" type="text" placeholder=".form-control-sm"&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Sizing Select</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <select class="form-control form-control-lg">
                                        <option>Large select</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control">
                                        <option>Default select</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <select class="form-control form-control-sm">
                                        <option>Small select</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;select class="form-control form-control-lg"&gt;
  &lt;option&gt;Large select&lt;/option&gt;
&lt;/select&gt;
&lt;select class="form-control"&gt;
  &lt;option&gt;Default select&lt;/option&gt;
&lt;/select&gt;
&lt;select class="form-control form-control-sm"&gt;
  &lt;option&gt;Small select&lt;/option&gt;
&lt;/select&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Readonly</h6>
                        <input class="form-control" type="text" placeholder="Readonly input here…" readonly>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-html">&lt;input class="form-control" type="text" placeholder="Readonly input here..." readonly&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Readonly plain text</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <form>
                                    <div class="form-group row">
                                        <label for="staticEmail2" class="col-sm-2 col-form-label">Email</label>
                                        <div class="col-sm-10">
                                            <input type="text" readonly class="form-control-plaintext"
                                                   id="staticEmail2" value="email@example.com">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputPassword2" class="col-sm-2 col-form-label">Password</label>
                                        <div class="col-sm-10">
                                            <input type="password" class="form-control" id="inputPassword2"
                                                   placeholder="Password">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
  &lt;div class="form-group row"&gt;
    &lt;label for="staticEmail" class="col-sm-2 col-form-label"&gt;Email&lt;/label&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;input type="text" readonly class="form-control-plaintext" id="staticEmail" value="email@example.com"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group row"&gt;
    &lt;label for="inputPassword" class="col-sm-2 col-form-label"&gt;Password&lt;/label&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;input type="password" class="form-control" id="inputPassword" placeholder="Password"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/form&gt;</code></pre>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <form class="form-inline">
                                    <div class="form-group mb-2">
                                        <label for="staticEmail3" class="sr-only">Email</label>
                                        <input type="text" readonly class="form-control-plaintext" id="staticEmail3"
                                               value="email@example.com">
                                    </div>
                                    <div class="form-group mx-sm-3 mb-2">
                                        <label for="inputPassword6" class="sr-only">Password</label>
                                        <input type="password" class="form-control" id="inputPassword6"
                                               placeholder="Password">
                                    </div>
                                    <button type="submit" class="btn btn-primary mb-2">Confirm identity</button>
                                </form>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form class="form-inline"&gt;
  &lt;div class="form-group mb-2"&gt;
    &lt;label for="staticEmail2" class="sr-only"&gt;Email&lt;/label&gt;
    &lt;input type="text" readonly class="form-control-plaintext" id="staticEmail2" value="email@example.com"&gt;
  &lt;/div&gt;
  &lt;div class="form-group mx-sm-3 mb-2"&gt;
    &lt;label for="inputPassword2" class="sr-only"&gt;Password&lt;/label&gt;
    &lt;input type="password" class="form-control" id="inputPassword2" placeholder="Password"&gt;
  &lt;/div&gt;
  &lt;button type="submit" class="btn btn-primary mb-2"&gt;Confirm identity&lt;/button&gt;
&lt;/form&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Range Inputs</h6>
                        <p>Set horizontally scrollable range inputs using <code>.form-control-range</code>.</p>
                        <div class="row">
                            <div class="col-md-8">
                                <form>
                                    <div class="form-group">
                                        <label for="formControlRange">Example Range input</label>
                                        <input type="range" class="form-control-range" id="formControlRange">
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
  &lt;div class="form-group"&gt;
    &lt;label for="formControlRange"&gt;Example Range input&lt;/label&gt;
    &lt;input type="range" class="form-control-range" id="formControlRange"&gt;
  &lt;/div&gt;
&lt;/form&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Checkboxes and radios</h6>
                        <p>By default, any number of checkboxes and radios that are immediate sibling will be
                            vertically stacked and appropriately spaced with <code>.form-check</code>.</p>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck3">
                                    <label class="form-check-label" for="defaultCheck3">
                                        Default checkbox
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck4"
                                           disabled>
                                    <label class="form-check-label" for="defaultCheck4">
                                        Disabled checkbox
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-check"&gt;
  &lt;input class="form-check-input" type="checkbox" value="" id="defaultCheck1"&gt;
  &lt;label class="form-check-label" for="defaultCheck1"&gt;
    Default checkbox
  &lt;/label&gt;
&lt;/div&gt;
&lt;div class="form-check"&gt;
  &lt;input class="form-check-input" type="checkbox" value="" id="defaultCheck2" disabled&gt;
  &lt;label class="form-check-label" for="defaultCheck2"&gt;
    Disabled checkbox
  &lt;/label&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="exampleRadios"
                                           id="exampleRadios4" value="option1" checked>
                                    <label class="form-check-label" for="exampleRadios4">
                                        Default radio
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="exampleRadios"
                                           id="exampleRadios5" value="option2">
                                    <label class="form-check-label" for="exampleRadios5">
                                        Second default radio
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="exampleRadios"
                                           id="exampleRadios6" value="option3" disabled>
                                    <label class="form-check-label" for="exampleRadios6">
                                        Disabled radio
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-check"&gt;
  &lt;input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked&gt;
  &lt;label class="form-check-label" for="exampleRadios1"&gt;
    Default radio
  &lt;/label&gt;
&lt;/div&gt;
&lt;div class="form-check"&gt;
  &lt;input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2"&gt;
  &lt;label class="form-check-label" for="exampleRadios2"&gt;
    Second default radio
  &lt;/label&gt;
&lt;/div&gt;
&lt;div class="form-check"&gt;
  &lt;input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option3" disabled&gt;
  &lt;label class="form-check-label" for="exampleRadios3"&gt;
    Disabled radio
  &lt;/label&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <h3>Inline</h3>
                        <p>Group checkboxes or radios on the same horizontal row by adding .form-check-inline to any
                            .form-check.</p>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox4"
                                           value="option1">
                                    <label class="form-check-label" for="inlineCheckbox4">1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox5"
                                           value="option2">
                                    <label class="form-check-label" for="inlineCheckbox5">2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" id="inlineCheckbox6"
                                           value="option3" disabled>
                                    <label class="form-check-label" for="inlineCheckbox6">3 (disabled)</label>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-check form-check-inline"&gt;
  &lt;input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1"&gt;
  &lt;label class="form-check-label" for="inlineCheckbox1"&gt;1&lt;/label&gt;
&lt;/div&gt;
&lt;div class="form-check form-check-inline"&gt;
  &lt;input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2"&gt;
  &lt;label class="form-check-label" for="inlineCheckbox2"&gt;2&lt;/label&gt;
&lt;/div&gt;
&lt;div class="form-check form-check-inline"&gt;
  &lt;input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3" disabled&gt;
  &lt;label class="form-check-label" for="inlineCheckbox3"&gt;3 (disabled)&lt;/label&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                           id="inlineRadio1" value="option1">
                                    <label class="form-check-label" for="inlineRadio1">1</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                           id="inlineRadio2" value="option2">
                                    <label class="form-check-label" for="inlineRadio2">2</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inlineRadioOptions"
                                           id="inlineRadio3" value="option3" disabled>
                                    <label class="form-check-label" for="inlineRadio3">3 (disabled)</label>
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-check form-check-inline"&gt;
  &lt;input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1"&gt;
  &lt;label class="form-check-label" for="inlineCheckbox1"&gt;1&lt;/label&gt;
&lt;/div&gt;
&lt;div class="form-check form-check-inline"&gt;
  &lt;input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2"&gt;
  &lt;label class="form-check-label" for="inlineCheckbox2"&gt;2&lt;/label&gt;
&lt;/div&gt;
&lt;div class="form-check form-check-inline"&gt;
  &lt;input class="form-check-input" type="checkbox" id="inlineCheckbox3" value="option3" disabled&gt;
  &lt;label class="form-check-label" for="inlineCheckbox3"&gt;3 (disabled)&lt;/label&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <h3>Without labels</h3>
                        <p>Add <code>.position-static</code> to inputs within <code>.form-check</code> that don’t
                            have any label text. Remember to still provide some form of label for assistive
                            technologies (for instance, using <code>aria-label</code>).</p>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-check">
                                    <input class="form-check-input position-static" type="checkbox"
                                           id="blankCheckbox" value="option1" aria-label="...">
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input position-static" type="radio" name="blankRadio"
                                           id="blankRadio1" value="option1" aria-label="...">
                                </div>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-check"&gt;
  &lt;input class="form-check-input position-static" type="checkbox" id="blankCheckbox" value="option1" aria-label="..."&gt;
&lt;/div&gt;
&lt;div class="form-check"&gt;
  &lt;input class="form-check-input position-static" type="radio" name="blankRadio" id="blankRadio1" value="option1" aria-label="..."&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Layout</h6>
                        <h4>Form row</h4>
                        <p>You may also swap <code>.row</code> for <code>.form-row</code>, a variation of our
                            standard grid row that overrides the default column gutters for tighter and more compact
                            layouts.</p>
                        <div class="row">
                            <div class="col-md-8">
                                <form>
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="text" class="form-control" placeholder="First name">
                                        </div>
                                        <div class="col">
                                            <input type="text" class="form-control" placeholder="Last name">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
  &lt;div class="form-row"&gt;
    &lt;div class="col"&gt;
      &lt;input type="text" class="form-control" placeholder="First name"&gt;
    &lt;/div&gt;
    &lt;div class="col"&gt;
      &lt;input type="text" class="form-control" placeholder="Last name"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/form&gt;</code></pre>
                        </div>
                        <p>More complex layouts can also be created with the grid system.</p>
                        <form>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Email</label>
                                    <input type="email" class="form-control" id="inputEmail4" placeholder="Email">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">Password</label>
                                    <input type="password" class="form-control" id="inputPassword4"
                                           placeholder="Password">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputAddress">Address</label>
                                <input type="text" class="form-control" id="inputAddress"
                                       placeholder="1234 Main St">
                            </div>
                            <div class="form-group">
                                <label for="inputAddress2">Address 2</label>
                                <input type="text" class="form-control" id="inputAddress2"
                                       placeholder="Apartment, studio, or floor">
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputCity">City</label>
                                    <input type="text" class="form-control" id="inputCity">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="inputState">State</label>
                                    <select id="inputState" class="form-control">
                                        <option selected>Choose...</option>
                                        <option>...</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="inputZip">Zip</label>
                                    <input type="text" class="form-control" id="inputZip">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="gridCheck">
                                    <label class="form-check-label" for="gridCheck">
                                        Check me out
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Sign in</button>
                        </form>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
  &lt;div class="form-row"&gt;
    &lt;div class="form-group col-md-6"&gt;
      &lt;label for="inputEmail4"&gt;Email&lt;/label&gt;
      &lt;input type="email" class="form-control" id="inputEmail4" placeholder="Email"&gt;
    &lt;/div&gt;
    &lt;div class="form-group col-md-6"&gt;
      &lt;label for="inputPassword4"&gt;Password&lt;/label&gt;
      &lt;input type="password" class="form-control" id="inputPassword4" placeholder="Password"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group"&gt;
    &lt;label for="inputAddress"&gt;Address&lt;/label&gt;
    &lt;input type="text" class="form-control" id="inputAddress" placeholder="1234 Main St"&gt;
  &lt;/div&gt;
  &lt;div class="form-group"&gt;
    &lt;label for="inputAddress2"&gt;Address 2&lt;/label&gt;
    &lt;input type="text" class="form-control" id="inputAddress2" placeholder="Apartment, studio, or floor"&gt;
  &lt;/div&gt;
  &lt;div class="form-row"&gt;
    &lt;div class="form-group col-md-6"&gt;
      &lt;label for="inputCity"&gt;City&lt;/label&gt;
      &lt;input type="text" class="form-control" id="inputCity"&gt;
    &lt;/div&gt;
    &lt;div class="form-group col-md-4"&gt;
      &lt;label for="inputState"&gt;State&lt;/label&gt;
      &lt;select id="inputState" class="form-control"&gt;
        &lt;option selected&gt;Choose...&lt;/option&gt;
        &lt;option&gt;...&lt;/option&gt;
      &lt;/select&gt;
    &lt;/div&gt;
    &lt;div class="form-group col-md-2"&gt;
      &lt;label for="inputZip"&gt;Zip&lt;/label&gt;
      &lt;input type="text" class="form-control" id="inputZip"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group"&gt;
    &lt;div class="form-check"&gt;
      &lt;input class="form-check-input" type="checkbox" id="gridCheck"&gt;
      &lt;label class="form-check-label" for="gridCheck"&gt;
        Check me out
      &lt;/label&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;button type="submit" class="btn btn-primary"&gt;Sign in&lt;/button&gt;
&lt;/form&gt;</code></pre>
                        </div>
                        <h4>Horizontal form</h4>
                        <form>
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" class="form-control" id="inputPassword3"
                                           placeholder="Password">
                                </div>
                            </div>
                            <fieldset class="form-group">
                                <div class="row">
                                    <div class="col-form-label col-sm-2 pt-0">Radios</div>
                                    <div class="col-sm-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gridRadios"
                                                   id="gridRadios1" value="option1" checked>
                                            <label class="form-check-label" for="gridRadios1">
                                                First radio
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gridRadios"
                                                   id="gridRadios2" value="option2">
                                            <label class="form-check-label" for="gridRadios2">
                                                Second radio
                                            </label>
                                        </div>
                                        <div class="form-check disabled">
                                            <input class="form-check-input" type="radio" name="gridRadios"
                                                   id="gridRadios3" value="option3" disabled>
                                            <label class="form-check-label" for="gridRadios3">
                                                Third disabled radio
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group row">
                                <div class="col-sm-2">Checkbox</div>
                                <div class="col-sm-10">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="gridCheck1">
                                        <label class="form-check-label" for="gridCheck1">
                                            Example checkbox
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">Sign in</button>
                                </div>
                            </div>
                        </form>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
  &lt;div class="form-group row"&gt;
    &lt;label for="inputEmail3" class="col-sm-2 col-form-label"&gt;Email&lt;/label&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;input type="email" class="form-control" id="inputEmail3" placeholder="Email"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group row"&gt;
    &lt;label for="inputPassword3" class="col-sm-2 col-form-label"&gt;Password&lt;/label&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;input type="password" class="form-control" id="inputPassword3" placeholder="Password"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;fieldset class="form-group"&gt;
    &lt;div class="row"&gt;
      &lt;legend class="col-form-label col-sm-2 pt-0"&gt;Radios&lt;/legend&gt;
      &lt;div class="col-sm-10"&gt;
        &lt;div class="form-check"&gt;
          &lt;input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="option1" checked&gt;
          &lt;label class="form-check-label" for="gridRadios1"&gt;
            First radio
          &lt;/label&gt;
        &lt;/div&gt;
        &lt;div class="form-check"&gt;
          &lt;input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="option2"&gt;
          &lt;label class="form-check-label" for="gridRadios2"&gt;
            Second radio
          &lt;/label&gt;
        &lt;/div&gt;
        &lt;div class="form-check disabled"&gt;
          &lt;input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="option3" disabled&gt;
          &lt;label class="form-check-label" for="gridRadios3"&gt;
            Third disabled radio
          &lt;/label&gt;
        &lt;/div&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/fieldset&gt;
  &lt;div class="form-group row"&gt;
    &lt;div class="col-sm-2"&gt;Checkbox&lt;/div&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;div class="form-check"&gt;
        &lt;input class="form-check-input" type="checkbox" id="gridCheck1"&gt;
        &lt;label class="form-check-label" for="gridCheck1"&gt;
          Example checkbox
        &lt;/label&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group row"&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;button type="submit" class="btn btn-primary"&gt;Sign in&lt;/button&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/form&gt;</code></pre>
                        </div>
                        <h4>Horizontal form label sizing</h4>
                        <p>Be sure to use <code>.col-form-label-sm</code> or <code>.col-form-label-lg</code> to your
                            <code>&lt;label&gt;</code>s or <code>&lt;label&gt;</code>s to correctly follow the size
                            of <code>.form-control-lg</code> and <code>.form-control-sm</code>.</p>
                        <form>
                            <div class="form-group row">
                                <label for="colFormLabelSm"
                                       class="col-sm-2 col-form-label col-form-label-sm">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control form-control-sm" id="colFormLabelSm"
                                           placeholder="col-form-label-sm">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="colFormLabel" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="colFormLabel"
                                           placeholder="col-form-label">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="colFormLabelLg"
                                       class="col-sm-2 col-form-label col-form-label-lg">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control form-control-lg" id="colFormLabelLg"
                                           placeholder="col-form-label-lg">
                                </div>
                            </div>
                        </form>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
  &lt;div class="form-group row"&gt;
    &lt;label for="colFormLabelSm" class="col-sm-2 col-form-label col-form-label-sm"&gt;Email&lt;/label&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;input type="email" class="form-control form-control-sm" id="colFormLabelSm" placeholder="col-form-label-sm"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group row"&gt;
    &lt;label for="colFormLabel" class="col-sm-2 col-form-label"&gt;Email&lt;/label&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;input type="email" class="form-control" id="colFormLabel" placeholder="col-form-label"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
  &lt;div class="form-group row"&gt;
    &lt;label for="colFormLabelLg" class="col-sm-2 col-form-label col-form-label-lg"&gt;Email&lt;/label&gt;
    &lt;div class="col-sm-10"&gt;
      &lt;input type="email" class="form-control form-control-lg" id="colFormLabelLg" placeholder="col-form-label-lg"&gt;
    &lt;/div&gt;
  &lt;/div&gt;
&lt;/form&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Help text</h6>
                        <p>Block-level help text in forms can be created using <code>.form-text</code> (previously
                            known as <code>.help-block</code> in v3). Inline help text can be flexibly implemented
                            using any inline HTML element and utility classes like <code>.text-muted</code>.</p>
                        <div class="row">
                            <div class="col-md-8">
                                <label for="inputPassword5">Password</label>
                                <input type="password" id="inputPassword5" class="form-control"
                                       aria-describedby="passwordHelpBlock">
                                <small id="passwordHelpBlock" class="form-text text-muted">
                                    Your password must be 8-20 characters long, contain letters and numbers, and
                                    must not contain spaces, special characters, or emoji.
                                </small>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;label for="inputPassword5"&gt;Password&lt;/label&gt;
&lt;input type="password" id="inputPassword5" class="form-control" aria-describedby="passwordHelpBlock"&gt;
&lt;small id="passwordHelpBlock" class="form-text text-muted"&gt;
  Your password must be 8-20 characters long, contain letters and numbers, and must not contain spaces, special characters, or emoji.
&lt;/small&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Disabled forms</h6>
                        <p>Block-level help text in forms can be created using <code>.form-text</code> (previously
                            known as <code>.help-block</code> in v3). Inline help text can be flexibly implemented
                            using any inline HTML element and utility classes like <code>.text-muted</code>.</p>
                        <div class="row">
                            <div class="col-md-8">
                                <form>
                                    <fieldset disabled>
                                        <div class="form-group">
                                            <label for="disabledTextInput">Disabled input</label>
                                            <input type="text" id="disabledTextInput" class="form-control"
                                                   placeholder="Disabled input">
                                        </div>
                                        <div class="form-group">
                                            <label for="disabledSelect">Disabled select menu</label>
                                            <select id="disabledSelect" class="form-control">
                                                <option>Disabled select</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                       id="disabledFieldsetCheck" disabled>
                                                <label class="form-check-label" for="disabledFieldsetCheck">
                                                    Can't check this
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </fieldset>
                                </form>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;form&gt;
  &lt;fieldset disabled&gt;
    &lt;div class="form-group"&gt;
      &lt;label for="disabledTextInput"&gt;Disabled input&lt;/label&gt;
      &lt;input type="text" id="disabledTextInput" class="form-control" placeholder="Disabled input"&gt;
    &lt;/div&gt;
    &lt;div class="form-group"&gt;
      &lt;label for="disabledSelect"&gt;Disabled select menu&lt;/label&gt;
      &lt;select id="disabledSelect" class="form-control"&gt;
        &lt;option&gt;Disabled select&lt;/option&gt;
      &lt;/select&gt;
    &lt;/div&gt;
    &lt;div class="form-group"&gt;
      &lt;div class="form-check"&gt;
        &lt;input class="form-check-input" type="checkbox" id="disabledFieldsetCheck" disabled&gt;
        &lt;label class="form-check-label" for="disabledFieldsetCheck"&gt;
          Can't check this
        &lt;/label&gt;
      &lt;/div&gt;
    &lt;/div&gt;
    &lt;button type="submit" class="btn btn-primary"&gt;Submit&lt;/button&gt;
  &lt;/fieldset&gt;
&lt;/form&gt;</code></pre>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </div>

@endsection

@push('script')
    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endpush
