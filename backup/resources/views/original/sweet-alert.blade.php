@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Sweet Alert</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Plugins</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Sweet Alert</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-primary sweet-basic">Basic</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                            <pre><code class="language-js">swal('Hello world!');</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-success sweet-success">Success</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code
                                        class="language-js">swal("Good job!", "You clicked the button!", "success");</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-warning sweet-warning">Warning</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code
                                        class="language-js">swal("Good job!", "You clicked the button!", "warning");</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-danger sweet-error">Error</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code
                                        class="language-js">swal("Good job!", "You clicked the button!", "error");</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-info sweet-info">Info</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code
                                        class="language-js">swal("Good job!", "You clicked the button!", "info");</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-primary sweet-multiple">Success or Cancel</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this imaginary file!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
})
    .then((willDelete) => {
    if (willDelete) {
        swal("Poof! Your imaginary file has been deleted!", {
            icon: "success",
        });
    } else {
        swal("Your imaginary file is safe!", {
    icon: "error",
});</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-primary sweet-prompt">Prompt</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">swal("Write something here:", {
    content: "input",
})
    .then((value) => {
    swal(`You typed: ${value}`);</code></pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="pt-4 pb-4 text-center">
                            <button type="button" class="btn btn-primary sweet-ajax">Ajax</button>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-js">swal({
    text: 'Search for a movie. e.g. "La La Land".',
    content: "input",
    button: {
        text: "Search!",
        closeModal: false,
    },
})
    .then(name => {
        if (!name) throw null;
        return fetch(`https://itunes.apple.com/search?term=${name}&entity=movie`);
    })
    .then(results => {
        return results.json();
    })
    .then(json => {
        const movie = json.results[0];
        if (!movie) {
            return swal("No movie was found!");
        }
        const name = movie.trackName;
        const imageURL = movie.artworkUrl100;
        swal({
            title: "Top result:",
            text: name,
            icon: imageURL,
        });
    })
    .catch(err => {
        if (err) {
            swal("Oh noes!", "The AJAX request failed!", "error");
        } else {
            swal.stopLoading();
            swal.close();
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
    <!-- Sweet alert -->
    <script src="{{ url('assets/js/examples/sweet-alert.js') }}"></script>

    <!-- Prism -->
    <script src="{{ url('vendors/prism/prism.js') }}"></script>
@endsection
