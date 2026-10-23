@extends('layouts.app')

@section('head')

    <!-- Datepicker -->
    <link rel="stylesheet" href="{{ url('vendors/datepicker/daterangepicker.css') }}">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">

@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Datepicker</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Forms</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Datepicker</li>
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
                            A JavaScript component for choosing date ranges, dates and times. <a
                                href="http://www.daterangepicker.com/" class="link-1" target="_blank">Plugin
                                page</a>.
                        </p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Css --&gt;
&lt;link rel="stylesheet" href="vendors/datepicker/daterangepicker.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/datepicker/daterangepicker.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Single Date Picker</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="single-date-picker" class="form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;input type="text" name="daterangepicker" class="form-control"&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input[name="daterangepicker"]').daterangepicker({
  singleDatePicker: true,
  showDropdowns: true
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Simple Date Range Picker</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="simple-date-range-picker" class="form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;input type="text" name="daterangepicker" class="form-control"&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code
                                        class="language-js">$('input[name="daterangepicker"]').daterangepicker();</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Simple Date Range Picker With a Callback</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="simple-date-range-picker-callback" class="form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;input type="text" name="daterangepicker" class="form-control"&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                        <pre><code class="language-js">$('input[name="daterangepicker"]').daterangepicker({
    opens: 'left'
}, function (start, end, label) {
    swal("A new date selection was made", start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'), "success")
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Date Range Picker With Times</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="datetimes" class="form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;input type="text" name="datetimes" class="form-control"&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">$('input[name="datetimes"]').daterangepicker({
    timePicker: true,
    startDate: moment().startOf('hour'),
    endDate: moment().startOf('hour').add(32, 'hour'),
    locale: {
        format: 'M/DD hh:mm A'
    }
});</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Input Initially Empty</h6>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" name="datefilter" class="form-control">
                            </div>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                            <pre><code class="language-html">&lt;input type="text" name="datefilter" class="form-control"&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                <pre><code class="language-js">var datefilter = $('input[name="datefilter"]');
datefilter.daterangepicker({
    autoUpdateInput: false,
    locale: {
        cancelLabel: 'Clear'
    }
});

datefilter.on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
});

$('input.create-event-datepicker').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false
}).on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('MM/DD/YYYY'));
});</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')

    <!-- Datepicker -->
    <script src="{{ url('vendors/datepicker/daterangepicker.js') }}"></script>
    <script src="{{ url('assets/js/examples/datepicker.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>

@endsection
