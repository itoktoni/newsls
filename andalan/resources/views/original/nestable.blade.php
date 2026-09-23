@extends('layouts.app')

@section('head')
    <!-- Nestable -->
    <link rel="stylesheet" href="{{ url('vendors/nestable/nestable.css') }}" type="text/css">

    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Nestable</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Plugins</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Nestable</li>
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
                        <p class="lead">Drag & drop hierarchical list with mouse and touch compatibility (jQuery
                            plugin).
                            <a href="https://dbushell.com/Nestable/" class="link-1">Demo page</a>.</p>
                        <div data-label="INCLUDED FILES" class="demo-code-preview">
                                <pre><code class="language-html">&lt;!-- Style --&gt;
&lt;link rel="stylesheet" href="vendors/nestable/nestable.css" type="text/css"&gt;

&lt;!-- Javascript --&gt;
&lt;script src="vendors/nestable/jquery.nestable.js"&gt;&lt;/script&gt;</code></pre>
                        </div>
                        <div data-label="HTML" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="dd"&gt;
    &lt;ol class="dd-list"&gt;
        &lt;li class="dd-item" data-id="1"&gt;
            &lt;div class="dd-handle"&gt;Item 1&lt;/div&gt;
        &lt;/li&gt;
        &lt;li class="dd-item" data-id="2"&gt;
            &lt;div class="dd-handle"&gt;Item 2&lt;/div&gt;
        &lt;/li&gt;
        &lt;li class="dd-item" data-id="3"&gt;
            &lt;div class="dd-handle"&gt;Item 3&lt;/div&gt;
            &lt;ol class="dd-list"&gt;
                &lt;li class="dd-item" data-id="4"&gt;
                    &lt;div class="dd-handle"&gt;Item 4&lt;/div&gt;
                &lt;/li&gt;
                &lt;li class="dd-item" data-id="5"&gt;
                    &lt;div class="dd-handle"&gt;Item 5&lt;/div&gt;
                &lt;/li&gt;
            &lt;/ol&gt;
        &lt;/li&gt;
    &lt;/ol&gt;
&lt;/div&gt;</code></pre>
                        </div>
                        <div data-label="JS" class="demo-code-preview">
                                    <pre><code
                                            class="language-js">$('.dd').nestable({ /* config options */ });</code></pre>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="mb-4" id="nestable-menu">
            <button type="button" class="btn btn-primary mr-2" data-action="expand-all">Expand All</button>
            <button type="button" class="btn btn-primary" data-action="collapse-all">Collapse All</button>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Draggable Handles</h6>
                        <div class="dd" id="nestable2">
                            <ol class="dd-list">
                                <li class="dd-item" data-id="1">
                                    <div class="dd-handle">Item 1</div>
                                </li>
                                <li class="dd-item" data-id="2">
                                    <div class="dd-handle">Item 2</div>
                                </li>
                                <li class="dd-item" data-id="3">
                                    <div class="dd-handle">Item 3</div>
                                    <ol class="dd-list">
                                        <li class="dd-item" data-id="4">
                                            <div class="dd-handle">Item 4</div>
                                        </li>
                                        <li class="dd-item" data-id="5">
                                            <div class="dd-handle">Item 5</div>
                                        </li>
                                        <li class="dd-item" data-id="6">
                                            <div class="dd-handle">Item 6</div>
                                        </li>
                                    </ol>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <textarea id="nestable2-output" class="form-control"></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Draggable Handles</h6>
                        <div class="dd" id="nestable1">
                            <ol class="dd-list">
                                <li class="dd-item dd3-item" data-id="13">
                                    <div class="dd-handle dd3-handle"></div>
                                    <div class="dd3-content">Item 13</div>
                                </li>
                                <li class="dd-item dd3-item" data-id="14">
                                    <div class="dd-handle dd3-handle"></div>
                                    <div class="dd3-content">Item 14</div>
                                </li>
                                <li class="dd-item dd3-item" data-id="15">
                                    <div class="dd-handle dd3-handle"></div>
                                    <div class="dd3-content">Item 15</div>
                                    <ol class="dd-list">
                                        <li class="dd-item dd3-item" data-id="16">
                                            <div class="dd-handle dd3-handle"></div>
                                            <div class="dd3-content">Item 16</div>
                                        </li>
                                        <li class="dd-item dd3-item" data-id="17">
                                            <div class="dd-handle dd3-handle"></div>
                                            <div class="dd3-content">Item 17</div>
                                        </li>
                                        <li class="dd-item dd3-item" data-id="18">
                                            <div class="dd-handle dd3-handle"></div>
                                            <div class="dd3-content">Item 18</div>
                                        </li>
                                    </ol>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <textarea id="nestable-output" class="form-control"></textarea>
                </div>
            </div>
        </div>

    </div>

@endsection

@section('script')
    <!-- begin::nestable -->
    <script src="{{ url('vendors/nestable/jquery.nestable.js') }}"></script>
    <script>
        $(function () {
            var updateOutput = function (e) {
                var list = e.length ? e : $(e.target),
                    output = list.data('output');
                if (window.JSON) {
                    output.val(window.JSON.stringify(list.nestable('serialize')));//, null, 2));
                } else {
                    output.val('JSON browser support required for this demo.');
                }
            };

            $('#nestable1').nestable().on('change', updateOutput);

            $('#nestable2').nestable({
                group: 1
            }).on('change', updateOutput);

            updateOutput($('#nestable1').data('output', $('#nestable-output')));
            updateOutput($('#nestable2').data('output', $('#nestable2-output')));

            $('#nestable-menu').on('click', function (e) {
                var target = $(e.target),
                    action = target.data('action');
                if (action === 'expand-all') {
                    $('.dd').nestable('expandAll');
                }
                if (action === 'collapse-all') {
                    $('.dd').nestable('collapseAll');
                }
            });
        });
    </script>
    <!-- end::nestable -->

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
