@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Tables</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Tables</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Basic</li>
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
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                    <th class="text-right" scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <button class="dropdown-item" type="button">Action</button>
                                                <button class="dropdown-item" type="button">Another action</button>
                                                <button class="dropdown-item" type="button">Something else here
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <button class="dropdown-item" type="button">Action</button>
                                                <button class="dropdown-item" type="button">Another action</button>
                                                <button class="dropdown-item" type="button">Something else here
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>@twitter</td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a href="#" class="btn btn-sm"
                                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <button class="dropdown-item" type="button">Action</button>
                                                <button class="dropdown-item" type="button">Another action</button>
                                                <button class="dropdown-item" type="button">Something else here
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
        &lt;th class="text-right" scope="col"&gt;Action&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
        &lt;td class="text-right"&gt;
          &lt;div class="dropdown"&gt;
            &lt;a href="#" class="btn btn-sm"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"&gt;
              &lt;i class="fa fa-ellipsis-v" aria-hidden="true"&gt;&lt;/i&gt;
            &lt;/a&gt;
            &lt;div class="dropdown-menu dropdown-menu-right"&gt;
              &lt;button class="dropdown-item" type="button"&gt;Action&lt;/button&gt;
              &lt;button class="dropdown-item" type="button"&gt;Another action&lt;/button&gt;
              &lt;button class="dropdown-item" type="button"&gt;Something else here&lt;/button&gt;
            &lt;/div&gt;
          &lt;/div&gt;
        &lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
        &lt;td class="text-right"&gt;
          &lt;div class="dropdown"&gt;
            &lt;a href="#" class="btn btn-sm"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"&gt;
              &lt;i class="fa fa-ellipsis-v" aria-hidden="true"&gt;&lt;/i&gt;
            &lt;/a&gt;
            &lt;div class="dropdown-menu dropdown-menu-right"&gt;
              &lt;button class="dropdown-item" type="button"&gt;Action&lt;/button&gt;
              &lt;button class="dropdown-item" type="button"&gt;Another action&lt;/button&gt;
              &lt;button class="dropdown-item" type="button"&gt;Something else here&lt;/button&gt;
            &lt;/div&gt;
          &lt;/div&gt;
        &lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
        &lt;td class="text-right"&gt;
          &lt;div class="dropdown"&gt;
            &lt;a href="#" class="btn btn-sm"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"&gt;
              &lt;i class="fa fa-ellipsis-v" aria-hidden="true"&gt;&lt;/i&gt;
            &lt;/a&gt;
            &lt;div class="dropdown-menu dropdown-menu-right"&gt;
              &lt;button class="dropdown-item" type="button"&gt;Action&lt;/button&gt;
              &lt;button class="dropdown-item" type="button"&gt;Another action&lt;/button&gt;
              &lt;button class="dropdown-item" type="button"&gt;Something else here&lt;/button&gt;
            &lt;/div&gt;
          &lt;/div&gt;
        &lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Avatar</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <th>
                                        <figure class="avatar avatar-sm">
                                            <img src="{{ url('assets/media/image/user/women_avatar5.jpg') }}"
                                                 class="rounded-circle" alt="image">
                                        </figure>
                                    </th>
                                    <td>Jessie</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <th>
                                        <figure class="avatar avatar-sm">
                                            <a href="#">
                                                <span class="avatar-title bg-info rounded-circle">J</span>
                                            </a>
                                        </figure>
                                    </th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <th>
                                        <figure class="avatar avatar-sm">
                                            <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}"
                                                 class="rounded-circle" alt="image">
                                        </figure>
                                    </th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <th>
                                        <figure class="avatar avatar-sm">
                                            <a href="#">
                                                <span class="avatar-title bg-warning rounded-circle">M</span>
                                            </a>
                                        </figure>
                                    </th>
                                    <td>Mark</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
    &lt;table class="table"&gt;
        &lt;thead&gt;
        &lt;tr&gt;
            &lt;th scope="col"&gt;#&lt;/th&gt;
            &lt;th scope="col"&gt;Avatar&lt;/th&gt;
            &lt;th scope="col"&gt;First&lt;/th&gt;
            &lt;th scope="col"&gt;Last&lt;/th&gt;
            &lt;th scope="col"&gt;Handle&lt;/th&gt;
        &lt;/tr&gt;
        &lt;/thead&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
            &lt;th scope="row"&gt;1&lt;/th&gt;
            &lt;th&gt;
                &lt;figure class="avatar avatar-sm"&gt;
                    &lt;img src="{{ url('assets/media/image/user/women_avatar5.jpg') }}" class="rounded-circle" alt="image"&gt;
                &lt;/figure&gt;
            &lt;/th&gt;
            &lt;td&gt;Jessie&lt;/td&gt;
            &lt;td&gt;Otto&lt;/td&gt;
            &lt;td&gt;@mdo&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
            &lt;th scope="row"&gt;2&lt;/th&gt;
            &lt;th&gt;
                &lt;figure class="avatar avatar-sm"&gt;
                    &lt;a href="#"&gt;
                        &lt;span class="avatar-title bg-info rounded-circle"&gt;J&lt;/span&gt;
                    &lt;/a&gt;
                &lt;/figure&gt;
            &lt;/th&gt;
            &lt;td&gt;Jacob&lt;/td&gt;
            &lt;td&gt;Thornton&lt;/td&gt;
            &lt;td&gt;@fat&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
            &lt;th scope="row"&gt;3&lt;/th&gt;
            &lt;th&gt;
                &lt;figure class="avatar avatar-sm"&gt;
                    &lt;img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}" class="rounded-circle" alt="image"&gt;
                &lt;/figure&gt;
            &lt;/th&gt;
            &lt;td&gt;Larry&lt;/td&gt;
            &lt;td&gt;the Bird&lt;/td&gt;
            &lt;td&gt;@twitter&lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
            &lt;th scope="row"&gt;2&lt;/th&gt;
            &lt;th&gt;
                &lt;figure class="avatar avatar-sm"&gt;
                    &lt;a href="#"&gt;
                        &lt;span class="avatar-title bg-warning rounded-circle"&gt;M&lt;/span&gt;
                    &lt;/a&gt;
                &lt;/figure&gt;
            &lt;/th&gt;
            &lt;td&gt;Mark&lt;/td&gt;
            &lt;td&gt;Thornton&lt;/td&gt;
            &lt;td&gt;@fat&lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
    &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Avatar</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <th>
                                        <figure class="avatar avatar-sm">
                                            <img src="{{ url('assets/media/image/user/women_avatar5.jpg') }}"
                                                 class="rounded-circle" alt="image">
                                        </figure>
                                    </th>
                                    <td>Jessie</td>
                                    <td>Otto</td>
                                    <td>
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <th>
                                        <figure class="avatar avatar-sm">
                                            <a href="#">
                                                <span class="avatar-title bg-primary rounded-circle">M</span>
                                            </a>
                                        </figure>
                                    </th>
                                    <td>Morn</td>
                                    <td>Thornton</td>
                                    <td>
                                        <span class="badge badge-danger">Passive</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <th>
                                        <figure class="avatar avatar-sm">
                                            <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}"
                                                 class="rounded-circle" alt="image">
                                        </figure>
                                    </th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>
                                        <span class="badge badge-warning">Pending</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
    &lt;table class="table"&gt;
        &lt;thead&gt;
        &lt;tr&gt;
            &lt;th scope="col"&gt;#&lt;/th&gt;
            &lt;th scope="col"&gt;Avatar&lt;/th&gt;
            &lt;th scope="col"&gt;First&lt;/th&gt;
            &lt;th scope="col"&gt;Last&lt;/th&gt;
            &lt;th scope="col"&gt;Status&lt;/th&gt;
        &lt;/tr&gt;
        &lt;/thead&gt;
        &lt;tbody&gt;
        &lt;tr&gt;
            &lt;th scope="row"&gt;1&lt;/th&gt;
            &lt;th&gt;
                &lt;figure class="avatar avatar-sm"&gt;
                    &lt;img src="{{ url('assets/media/image/user/women_avatar5.jpg') }}" class="rounded-circle" alt="image"&gt;
                &lt;/figure&gt;
            &lt;/th&gt;
            &lt;td&gt;Jessie&lt;/td&gt;
            &lt;td&gt;Otto&lt;/td&gt;
            &lt;td&gt;
                &lt;span class="badge badge-success"&gt;Active&lt;/span&gt;
            &lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
            &lt;th scope="row"&gt;2&lt;/th&gt;
            &lt;th&gt;
                &lt;figure class="avatar avatar-sm"&gt;
                    &lt;a href="#"&gt;
                        &lt;span class="avatar-title bg-primary rounded-circle"&gt;M&lt;/span&gt;
                    &lt;/a&gt;
                &lt;/figure&gt;
            &lt;/th&gt;
            &lt;td&gt;Morn&lt;/td&gt;
            &lt;td&gt;Thornton&lt;/td&gt;
            &lt;td&gt;
                &lt;span class="badge badge-danger"&gt;Passive&lt;/span&gt;
            &lt;/td&gt;
        &lt;/tr&gt;
        &lt;tr&gt;
            &lt;th scope="row"&gt;3&lt;/th&gt;
            &lt;th&gt;
                &lt;figure class="avatar avatar-sm"&gt;
                    &lt;img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}" class="rounded-circle" alt="image"&gt;
                &lt;/figure&gt;
            &lt;/th&gt;
            &lt;td&gt;Larry&lt;/td&gt;
            &lt;td&gt;the Bird&lt;/td&gt;
            &lt;td&gt;
                &lt;span class="badge badge-warning"&gt;Pending&lt;/span&gt;
            &lt;/td&gt;
        &lt;/tr&gt;
        &lt;/tbody&gt;
    &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Striped Rows</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table table-striped"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Bordered Table</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td colspan="2">Larry the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table table-bordered"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Borderless Table</h6>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td colspan="2">Larry the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table table-borderless"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Hoverable Rows</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td colspan="2">Larry the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table table-hover"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Small Table</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td colspan="2">Larry the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table table-small"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Table Head Options</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table"&gt;
    &lt;thead class="thead-dark"&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;

&lt;div class="table-responsive"&gt;
  &lt;table class="table"&gt;
    &lt;thead class="thead-light"&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Contextual Classes</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Class</th>
                                    <th scope="col">Heading</th>
                                    <th scope="col">Heading</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="table-active">
                                    <th scope="row">Active</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr>
                                    <th scope="row">Default</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-primary">
                                    <th scope="row">Primary</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-secondary">
                                    <th scope="row">Secondary</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-success">
                                    <th scope="row">Success</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-danger">
                                    <th scope="row">Danger</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-warning">
                                    <th scope="row">Warning</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-info">
                                    <th scope="row">Info</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-light">
                                    <th scope="row">Light</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="table-dark">
                                    <th scope="row">Dark</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;Class&lt;/th&gt;
        &lt;th scope="col"&gt;Heading&lt;/th&gt;
        &lt;th scope="col"&gt;Heading&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr class="table-active"&gt;
        &lt;th scope="row"&gt;Active&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;Default&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-primary"&gt;
        &lt;th scope="row"&gt;Primary&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-secondary"&gt;
        &lt;th scope="row"&gt;Secondary&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-success"&gt;
        &lt;th scope="row"&gt;Success&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-danger"&gt;
        &lt;th scope="row"&gt;Danger&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-warning"&gt;
        &lt;th scope="row"&gt;Warning&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-info"&gt;
        &lt;th scope="row"&gt;Info&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-light"&gt;
        &lt;th scope="row"&gt;Light&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="table-dark"&gt;
        &lt;th scope="row"&gt;Dark&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Contextual Classes</h6>
                        <div class="table-responsive">
                            <table class="table table-dark">
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Heading</th>
                                    <th scope="col">Heading</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="bg-primary">
                                    <th scope="row">1</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="bg-success">
                                    <th scope="row">3</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr>
                                    <th scope="row">4</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="bg-info">
                                    <th scope="row">5</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr>
                                    <th scope="row">6</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="bg-warning">
                                    <th scope="row">7</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr>
                                    <th scope="row">8</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                <tr class="bg-danger">
                                    <th scope="row">9</th>
                                    <td>Cell</td>
                                    <td>Cell</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table table-dark"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;Heading&lt;/th&gt;
        &lt;th scope="col"&gt;Heading&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr class="bg-primary"&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="bg-success"&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;4&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="bg-info"&gt;
        &lt;th scope="row"&gt;5&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;6&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="bg-warning"&gt;
        &lt;th scope="row"&gt;7&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;8&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr class="bg-danger"&gt;
        &lt;th scope="row"&gt;9&lt;/th&gt;
        &lt;td&gt;Cell&lt;/td&gt;
        &lt;td&gt;Cell&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Captions</h6>
                        <div class="table-responsive">
                            <table class="table">
                                <caption>List of users</caption>
                                <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First</th>
                                    <th scope="col">Last</th>
                                    <th scope="col">Handle</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row">1</th>
                                    <td>Mark</td>
                                    <td>Otto</td>
                                    <td>@mdo</td>
                                </tr>
                                <tr>
                                    <th scope="row">2</th>
                                    <td>Jacob</td>
                                    <td>Thornton</td>
                                    <td>@fat</td>
                                </tr>
                                <tr>
                                    <th scope="row">3</th>
                                    <td>Larry</td>
                                    <td>the Bird</td>
                                    <td>@twitter</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt;
  &lt;table class="table"&gt;
    &lt;caption&gt;List of users&lt;/caption&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th scope="col"&gt;#&lt;/th&gt;
        &lt;th scope="col"&gt;First&lt;/th&gt;
        &lt;th scope="col"&gt;Last&lt;/th&gt;
        &lt;th scope="col"&gt;Handle&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;1&lt;/th&gt;
        &lt;td&gt;Mark&lt;/td&gt;
        &lt;td&gt;Otto&lt;/td&gt;
        &lt;td&gt;@mdo&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;2&lt;/th&gt;
        &lt;td&gt;Jacob&lt;/td&gt;
        &lt;td&gt;Thornton&lt;/td&gt;
        &lt;td&gt;@fat&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;th scope="row"&gt;3&lt;/th&gt;
        &lt;td&gt;Larry&lt;/td&gt;
        &lt;td&gt;the Bird&lt;/td&gt;
        &lt;td&gt;@twitter&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
  &lt;/table&gt;
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
