@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Custom Forms</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Forms</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Custom Forms</li>
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
                        <h6 class="card-title">Checkboxes</h6>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="customCheck" checked>
                                <label class="custom-control-label" for="customCheck">Check this custom checkbox -
                                    Default</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-checkbox-secondary">
                                <input type="checkbox" class="custom-control-input" id="customCheck1" checked>
                                <label class="custom-control-label" for="customCheck1">Check this custom checkbox -
                                    Secondary</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-checkbox-success">
                                <input type="checkbox" class="custom-control-input" id="customCheck2" checked>
                                <label class="custom-control-label" for="customCheck2">Check this custom checkbox -
                                    Success</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-checkbox-danger">
                                <input type="checkbox" class="custom-control-input" id="customCheck3" checked>
                                <label class="custom-control-label" for="customCheck3">Check this custom checkbox -
                                    Danger</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-checkbox-warning">
                                <input type="checkbox" class="custom-control-input" id="customCheck4" checked>
                                <label class="custom-control-label" for="customCheck4">Check this custom checkbox -
                                    Warning</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-checkbox-info">
                                <input type="checkbox" class="custom-control-input" id="customCheck5" checked>
                                <label class="custom-control-label" for="customCheck5">Check this custom checkbox -
                                    Info</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox custom-checkbox-dark">
                                <input type="checkbox" class="custom-control-input" id="customCheck6" checked>
                                <label class="custom-control-label" for="customCheck6">Check this custom checkbox -
                                    Dark</label>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-checkbox"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customCheck" checked&gt;
        &lt;label class="custom-control-label" for="customCheck"&gt;Check this custom checkbox - Default&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-checkbox custom-checkbox-secondary"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customCheck1" checked&gt;
        &lt;label class="custom-control-label" for="customCheck1"&gt;Check this custom checkbox - Secondary&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-checkbox custom-checkbox-success"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customCheck2" checked&gt;
        &lt;label class="custom-control-label" for="customCheck2"&gt;Check this custom checkbox - Success&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-checkbox custom-checkbox-danger"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customCheck3" checked&gt;
        &lt;label class="custom-control-label" for="customCheck3"&gt;Check this custom checkbox - Danger&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-checkbox custom-checkbox-warning"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customCheck4" checked&gt;
        &lt;label class="custom-control-label" for="customCheck4"&gt;Check this custom checkbox - Warning&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-checkbox custom-checkbox-info"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customCheck5" checked&gt;
        &lt;label class="custom-control-label" for="customCheck5"&gt;Check this custom checkbox - Info&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-checkbox custom-checkbox-dark"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customCheck6" checked&gt;
        &lt;label class="custom-control-label" for="customCheck6"&gt;Check this custom checkbox - Dark&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Radio</h6>
                        <div class="form-group">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="customRadio" name="customRadio" class="custom-control-input"
                                       checked>
                                <label class="custom-control-label" for="customRadio">Toggle this custom radio -
                                    Default</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio custom-checkbox-secondary">
                                <input type="radio" id="customRadio1" name="customRadio"
                                       class="custom-control-input">
                                <label class="custom-control-label" for="customRadio1">Toggle this custom radio -
                                    Secondary</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio custom-checkbox-success">
                                <input type="radio" id="customRadio2" name="customRadio"
                                       class="custom-control-input">
                                <label class="custom-control-label" for="customRadio2">Toggle this custom radio -
                                    Success</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio custom-checkbox-danger">
                                <input type="radio" id="customRadio3" name="customRadio"
                                       class="custom-control-input">
                                <label class="custom-control-label" for="customRadio3">Toggle this custom radio -
                                    Danger</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio custom-checkbox-warning">
                                <input type="radio" id="customRadio4" name="customRadio"
                                       class="custom-control-input">
                                <label class="custom-control-label" for="customRadio4">Toggle this custom radio -
                                    Warning</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio custom-checkbox-info">
                                <input type="radio" id="customRadio5" name="customRadio"
                                       class="custom-control-input">
                                <label class="custom-control-label" for="customRadio5">Toggle this custom radio -
                                    Info</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-radio custom-checkbox-dark">
                                <input type="radio" id="customRadio6" name="customRadio"
                                       class="custom-control-input">
                                <label class="custom-control-label" for="customRadio6">Toggle this custom radio -
                                    Dark</label>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-radio"&gt;
        &lt;input type="radio" id="customRadio" name="customRadio" class="custom-control-input" checked&gt;
        &lt;label class="custom-control-label" for="customRadio"&gt;Toggle this custom radio - Default&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-radio custom-checkbox-secondary"&gt;
        &lt;input type="radio" id="customRadio1" name="customRadio" class="custom-control-input"&gt;
        &lt;label class="custom-control-label" for="customRadio1"&gt;Toggle this custom radio - Secondary&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-radio custom-checkbox-success"&gt;
        &lt;input type="radio" id="customRadio2" name="customRadio" class="custom-control-input"&gt;
        &lt;label class="custom-control-label" for="customRadio2"&gt;Toggle this custom radio - Success&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-radio custom-checkbox-danger"&gt;
        &lt;input type="radio" id="customRadio3" name="customRadio" class="custom-control-input"&gt;
        &lt;label class="custom-control-label" for="customRadio3"&gt;Toggle this custom radio - Danger&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-radio custom-checkbox-warning"&gt;
        &lt;input type="radio" id="customRadio4" name="customRadio" class="custom-control-input"&gt;
        &lt;label class="custom-control-label" for="customRadio4"&gt;Toggle this custom radio -
            Warning&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-radio custom-checkbox-info"&gt;
        &lt;input type="radio" id="customRadio5" name="customRadio" class="custom-control-input"&gt;
        &lt;label class="custom-control-label" for="customRadio5"&gt;Toggle this custom radio - Info&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-radio custom-checkbox-dark"&gt;
        &lt;input type="radio" id="customRadio6" name="customRadio" class="custom-control-input"&gt;
        &lt;label class="custom-control-label" for="customRadio6"&gt;Toggle this custom radio -Dark&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Switches</h6>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitch" checked>
                                <label class="custom-control-label" for="customSwitch">Toggle this switch element -
                                    Default</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch custom-checkbox-secondary">
                                <input type="checkbox" class="custom-control-input" id="customSwitch1_" checked>
                                <label class="custom-control-label" for="customSwitch1_">Toggle this switch element
                                    -
                                    Secondary</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch custom-checkbox-success">
                                <input type="checkbox" class="custom-control-input" id="customSwitch2_" checked>
                                <label class="custom-control-label" for="customSwitch2_">Toggle this switch element
                                    -
                                    Success</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch custom-checkbox-danger">
                                <input type="checkbox" class="custom-control-input" id="customSwitch3_" checked>
                                <label class="custom-control-label" for="customSwitch3_">Toggle this switch element
                                    -
                                    Danger</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch custom-checkbox-warning">
                                <input type="checkbox" class="custom-control-input" id="customSwitch4_" checked>
                                <label class="custom-control-label" for="customSwitch4_">Toggle this switch element
                                    -
                                    Warning</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch custom-checkbox-info">
                                <input type="checkbox" class="custom-control-input" id="customSwitch5_" checked>
                                <label class="custom-control-label" for="customSwitch5_">Toggle this switch element
                                    -
                                    Info</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch custom-checkbox-dark">
                                <input type="checkbox" class="custom-control-input" id="customSwitch6_" checked>
                                <label class="custom-control-label" for="customSwitch6_">Toggle this switch element
                                    -
                                    Dark</label>
                            </div>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-html">&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-switch"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customSwitch1" checked&gt;
        &lt;label class="custom-control-label" for="customSwitch1"&gt;Toggle this switch element - Default&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-switch custom-checkbox-secondary"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customSwitch2" checked&gt;
        &lt;label class="custom-control-label" for="customSwitch2"&gt;Toggle this switch element - Secondary&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-switch custom-checkbox-success"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customSwitch3" checked&gt;
        &lt;label class="custom-control-label" for="customSwitch3"&gt;Toggle this switch element - Success&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-switch custom-checkbox-danger"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customSwitch4" checked&gt;
        &lt;label class="custom-control-label" for="customSwitch4"&gt;Toggle this switch element - Danger&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-switch custom-checkbox-warning"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customSwitch5" checked&gt;
        &lt;label class="custom-control-label" for="customSwitch5"&gt;Toggle this switch element - Warning&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-switch custom-checkbox-info"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customSwitch6" checked&gt;
        &lt;label class="custom-control-label" for="customSwitch6"&gt;Toggle this switch element - Info&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;div class="custom-control custom-switch custom-checkbox-dark"&gt;
        &lt;input type="checkbox" class="custom-control-input" id="customSwitch7" checked&gt;
        &lt;label class="custom-control-label" for="customSwitch7"&gt;Toggle this switch element - Dark&lt;/label&gt;
    &lt;/div&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Checkbox / Radio / Switches Inline</h6>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" id="customCheckBoxInline1" name="customCheckboxInline1"
                                   class="custom-control-input">
                            <label class="custom-control-label" for="customCheckBoxInline1">Check this custom
                                checkbox</label>
                        </div>
                        <div class="custom-control custom-checkbox custom-control-inline">
                            <input type="checkbox" id="customCheckBoxInline2" name="customCheckboxInline2"
                                   class="custom-control-input">
                            <label class="custom-control-label" for="customCheckBoxInline2">Check this custom
                                checkbox</label>
                        </div>
                        <hr>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline1" name="customRadioInline1"
                                   class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline1">Toggle this custom
                                radio</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline2" name="customRadioInline1"
                                   class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline2">Or toggle this other custom
                                radio</label>
                        </div>
                        <hr>
                        <div class="custom-control custom-switch custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="customSwitchInline1">
                            <label class="custom-control-label" for="customSwitchInline1">Disabled switch
                                element</label>
                        </div>
                        <div class="custom-control custom-switch custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="customSwitchInline2">
                            <label class="custom-control-label" for="customSwitchInline2">Disabled switch
                                element</label>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="custom-control custom-checkbox custom-control-inline"&gt;
    &lt;input type="checkbox" id="customCheckBoxInline1" name="customCheckboxInline1" class="custom-control-input"&gt;
    &lt;label class="custom-control-label" for="customCheckBoxInline1"&gt;Check this custom checkbox&lt;/label&gt;
&lt;/div&gt;
&lt;div class="custom-control custom-checkbox custom-control-inline"&gt;
    &lt;input type="checkbox" id="customCheckBoxInline2" name="customCheckboxInline2" class="custom-control-input"&gt;
    &lt;label class="custom-control-label" for="customCheckBoxInline2"&gt;Check this custom checkbox&lt;/label&gt;
&lt;/div&gt;

&lt;div class="custom-control custom-radio custom-control-inline"&gt;
    &lt;input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input"&gt;
    &lt;label class="custom-control-label" for="customRadioInline1"&gt;Toggle this custom radio&lt;/label&gt;
&lt;/div&gt;
&lt;div class="custom-control custom-radio custom-control-inline"&gt;
    &lt;input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input"&gt;
    &lt;label class="custom-control-label" for="customRadioInline2"&gt;Or toggle this other custom radio&lt;/label&gt;
&lt;/div&gt;

&lt;div class="custom-control custom-switch custom-control-inline"&gt;
    &lt;input type="checkbox" class="custom-control-input" id="customSwitchInline1"&gt;
    &lt;label class="custom-control-label" for="customSwitchInline1"&gt;Disabled switch element&lt;/label&gt;
&lt;/div&gt;
&lt;div class="custom-control custom-switch custom-control-inline"&gt;
    &lt;input type="checkbox" class="custom-control-input" id="customSwitchInline2"&gt;
    &lt;label class="custom-control-label" for="customSwitchInline2"&gt;Disabled switch element&lt;/label&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Checkbox / Radio / Switches Disabled</h6>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="customCheckDisabled1" disabled>
                            <label class="custom-control-label" for="customCheckDisabled1">Check this custom
                                checkbox</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" name="radioDisabled" id="customRadioDisabled2"
                                   class="custom-control-input" disabled>
                            <label class="custom-control-label" for="customRadioDisabled2">Toggle this custom
                                radio</label>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" disabled id="customSwitchDisabled1">
                            <label class="custom-control-label" for="customSwitchDisabled1">Disabled switch
                                element</label>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Checkbox --&gt;
&lt;div class="custom-control custom-checkbox"&gt;
    &lt;input type="checkbox" class="custom-control-input" id="customCheckDisabled" disabled&gt;
    &lt;label class="custom-control-label" for="customCheckDisabled"&gt;Check this custom checkbox&lt;/label&gt;
&lt;/div&gt;

&lt;!-- Radio --&gt;
&lt;div class="custom-control custom-radio"&gt;
    &lt;input type="radio" name="radioDisabled" id="customRadioDisabled" class="custom-control-input" disabled&gt;
    &lt;label class="custom-control-label" for="customRadioDisabled"&gt;Toggle this custom radio&lt;/label&gt;
&lt;/div&gt;

&lt;!-- Switch --&gt;
&lt;div class="custom-control custom-switch"&gt;
    &lt;input type="checkbox" class="custom-control-input" disabled id="customSwitchDisabled"&gt;
    &lt;label class="custom-control-label" for="customSwitchDisabled"&gt;Disabled switch element&lt;/label&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Select menu</h6>
                        <select class="custom-select custom-select-lg mb-3">
                            <option selected>Open this select menu</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                        <select class="custom-select mb-3">
                            <option selected>Open this select menu</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                        <select class="custom-select custom-select-sm">
                            <option selected>Open this select menu</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;select class="custom-select custom-select-lg"&gt;
  &lt;option selected&gt;Open this select menu&lt;/option&gt;
  &lt;option value="1"&gt;One&lt;/option&gt;
  &lt;option value="2"&gt;Two&lt;/option&gt;
  &lt;option value="3"&gt;Three&lt;/option&gt;
&lt;/select&gt;

&lt;select class="custom-select mb-3"&gt;
  &lt;option selected&gt;Open this select menu&lt;/option&gt;
  &lt;option value="1"&gt;One&lt;/option&gt;
  &lt;option value="2"&gt;Two&lt;/option&gt;
  &lt;option value="3"&gt;Three&lt;/option&gt;
&lt;/select&gt;

&lt;select class="custom-select custom-select-sm"&gt;
  &lt;option selected&gt;Open this select menu&lt;/option&gt;
  &lt;option value="1"&gt;One&lt;/option&gt;
  &lt;option value="2"&gt;Two&lt;/option&gt;
  &lt;option value="3"&gt;Three&lt;/option&gt;
&lt;/select&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Range</h6>
                        <div class="form-group">
                            <label for="customRange1">Default</label>
                            <input type="range" class="custom-range" id="customRange1">
                        </div>
                        <div class="form-group">
                            <label for="customRange6">Secondary</label>
                            <input type="range" value="20" class="custom-range custom-range-secondary"
                                   id="customRange6">
                        </div>
                        <div class="form-group">
                            <label for="customRange7">Success</label>
                            <input type="range" value="70" class="custom-range custom-range-success"
                                   id="customRange7">
                        </div>
                        <div class="form-group">
                            <label for="customRange9">Danger</label>
                            <input type="range" value="30" class="custom-range custom-range-danger"
                                   id="customRange9">
                        </div>
                        <div class="form-group">
                            <label for="customRange2">Warning</label>
                            <input type="range" value="50" class="custom-range custom-range-warning"
                                   id="customRange2">
                        </div>
                        <div class="form-group">
                            <label for="customRange8">Info</label>
                            <input type="range" class="custom-range custom-range-info" id="customRange8">
                        </div>
                        <div class="form-group">
                            <label for="customRange3">Dark</label>
                            <input type="range" value="10" class="custom-range custom-range-dark" id="customRange3">
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="form-group"&gt;
    &lt;label for="customRange1"&gt;Default&lt;/label&gt;
    &lt;input type="range" class="custom-range" id="customRange1"&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;label for="customRange6"&gt;Secondary&lt;/label&gt;
    &lt;input type="range" value="20" class="custom-range custom-range-secondary" id="customRange6"&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;label for="customRange7"&gt;Success&lt;/label&gt;
    &lt;input type="range" value="70" class="custom-range custom-range-success" id="customRange7"&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;label for="customRange9"&gt;Danger&lt;/label&gt;
    &lt;input type="range" value="30" class="custom-range custom-range-danger" id="customRange9"&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;label for="customRange2"&gt;Warning&lt;/label&gt;
    &lt;input type="range" value="50" class="custom-range custom-range-warning" id="customRange2"&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;label for="customRange8"&gt;Info&lt;/label&gt;
    &lt;input type="range" class="custom-range custom-range-info" id="customRange8"&gt;
&lt;/div&gt;
&lt;div class="form-group"&gt;
    &lt;label for="customRange3"&gt;Dark&lt;/label&gt;
    &lt;input type="range" value="10" class="custom-range custom-range-dark" id="customRange3"&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">File browser</h6>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile">
                            <label class="custom-file-label" for="customFile">Choose file</label>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="custom-file"&gt;
  &lt;input type="file" class="custom-file-input" id="customFile"&gt;
    &lt;label class="custom-file-label" for="customFile"&gt;Choose file&lt;/label&gt;
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
