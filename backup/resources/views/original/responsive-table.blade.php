@extends('layouts.app')

@section('head')
    <!-- Prism -->
    <link rel="stylesheet" href="{{ url('vendors/prism/prism.css') }}" type="text/css">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Responsive Tables</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Tables</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Responsive Tables</li>
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
                        <h6 class="card-title">Basic</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Age</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>Cara Stevens</td>
                                    <td>Sales Assistant</td>
                                    <td>New York</td>
                                    <td>46</td>
                                    <td>2011/12/06</td>
                                    <td>$145,600</td>
                                </tr>
                                <tr>
                                    <td>Hermione Butler</td>
                                    <td>Regional Director</td>
                                    <td>London</td>
                                    <td>47</td>
                                    <td>2011/03/21</td>
                                    <td>$356,250</td>
                                </tr>
                                <tr>
                                    <td>Lael Greer</td>
                                    <td>Systems Administrator</td>
                                    <td>London</td>
                                    <td>21</td>
                                    <td>2009/02/27</td>
                                    <td>$103,500</td>
                                </tr>
                                <tr>
                                    <td>Jonas Alexander</td>
                                    <td>Developer</td>
                                    <td>San Francisco</td>
                                    <td>30</td>
                                    <td>2010/07/14</td>
                                    <td>$86,500</td>
                                </tr>
                                <tr>
                                    <td>Shad Decker</td>
                                    <td>Regional Director</td>
                                    <td>Edinburgh</td>
                                    <td>51</td>
                                    <td>2008/11/13</td>
                                    <td>$183,000</td>
                                </tr>
                                <tr>
                                    <td>Michael Bruce</td>
                                    <td>Javascript Developer</td>
                                    <td>Singapore</td>
                                    <td>29</td>
                                    <td>2011/06/27</td>
                                    <td>$183,000</td>
                                </tr>
                                <tr>
                                    <td>Donna Snider</td>
                                    <td>Customer Support</td>
                                    <td>New York</td>
                                    <td>27</td>
                                    <td>2011/01/25</td>
                                    <td>$112,000</td>
                                </tr>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Age</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;div class="table-responsive"&gt; &lt;!-- Required for Responsive --&gt;
  &lt;table class="table table-striped table-bordered"&gt;
    &lt;thead&gt;
      &lt;tr&gt;
        &lt;th&gt;Name&lt;/th&gt;
        &lt;th&gt;Position&lt;/th&gt;
        &lt;th&gt;Office&lt;/th&gt;
        &lt;th&gt;Age&lt;/th&gt;
        &lt;th&gt;Start date&lt;/th&gt;
        &lt;th&gt;Salary&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/thead&gt;
    &lt;tbody&gt;
      &lt;tr&gt;
        &lt;td&gt;Cara Stevens&lt;/td&gt;
        &lt;td&gt;Sales Assistant&lt;/td&gt;
        &lt;td&gt;New York&lt;/td&gt;
        &lt;td&gt;46&lt;/td&gt;
        &lt;td&gt;2011/12/06&lt;/td&gt;
        &lt;td&gt;$145,600&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td&gt;Hermione Butler&lt;/td&gt;
        &lt;td&gt;Regional Director&lt;/td&gt;
        &lt;td&gt;London&lt;/td&gt;
        &lt;td&gt;47&lt;/td&gt;
        &lt;td&gt;2011/03/21&lt;/td&gt;
        &lt;td&gt;$356,250&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td&gt;Lael Greer&lt;/td&gt;
        &lt;td&gt;Systems Administrator&lt;/td&gt;
        &lt;td&gt;London&lt;/td&gt;
        &lt;td&gt;21&lt;/td&gt;
        &lt;td&gt;2009/02/27&lt;/td&gt;
        &lt;td&gt;$103,500&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td&gt;Jonas Alexander&lt;/td&gt;
        &lt;td&gt;Developer&lt;/td&gt;
        &lt;td&gt;San Francisco&lt;/td&gt;
        &lt;td&gt;30&lt;/td&gt;
        &lt;td&gt;2010/07/14&lt;/td&gt;
        &lt;td&gt;$86,500&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td&gt;Shad Decker&lt;/td&gt;
        &lt;td&gt;Regional Director&lt;/td&gt;
        &lt;td&gt;Edinburgh&lt;/td&gt;
        &lt;td&gt;51&lt;/td&gt;
        &lt;td&gt;2008/11/13&lt;/td&gt;
        &lt;td&gt;$183,000&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td&gt;Michael Bruce&lt;/td&gt;
        &lt;td&gt;Javascript Developer&lt;/td&gt;
        &lt;td&gt;Singapore&lt;/td&gt;
        &lt;td&gt;29&lt;/td&gt;
        &lt;td&gt;2011/06/27&lt;/td&gt;
        &lt;td&gt;$183,000&lt;/td&gt;
      &lt;/tr&gt;
      &lt;tr&gt;
        &lt;td&gt;Donna Snider&lt;/td&gt;
        &lt;td&gt;Customer Support&lt;/td&gt;
        &lt;td&gt;New York&lt;/td&gt;
        &lt;td&gt;27&lt;/td&gt;
        &lt;td&gt;2011/01/25&lt;/td&gt;
        &lt;td&gt;$112,000&lt;/td&gt;
      &lt;/tr&gt;
    &lt;/tbody&gt;
    &lt;tfoot&gt;
      &lt;tr&gt;
        &lt;th&gt;Name&lt;/th&gt;
        &lt;th&gt;Position&lt;/th&gt;
        &lt;th&gt;Office&lt;/th&gt;
        &lt;th&gt;Age&lt;/th&gt;
        &lt;th&gt;Start date&lt;/th&gt;
        &lt;th&gt;Salary&lt;/th&gt;
      &lt;/tr&gt;
    &lt;/tfoot&gt;
  &lt;/table&gt;
&lt;/div&gt;</code></pre>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Javascript Table</h6>
                        <p>For mobile-compatible tables, add the <code>.table-responsive-stack</code> class.</p>
                        <div class="alert alert-info alert-with-border alert-dismissible" role="alert">
                            <i class="ti-alert m-r-10"></i> Open your browser in mobile mode.
                        </div>
                        <table class="table table-bordered table-striped table-responsive-stack">
                            <thead class="thead-dark">
                            <tr>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Taste</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Apple</td>
                                <td>Green and Red</td>
                                <td>Sweet and Tart</td>
                            </tr>
                            <tr>
                                <td>Banana</td>
                                <td>Yellow</td>
                                <td>Sweet and Mushy</td>
                            </tr>
                            <tr>
                                <td>Mango</td>
                                <td>Yellowish Green</td>
                                <td>Tangy</td>
                            </tr>
                            <tr>
                                <td>Orange</td>
                                <td>Orange</td>
                                <td>Sweet and Tangy</td>
                            </tr>
                            <tr>
                                <td>Blueberry</td>
                                <td>Blue</td>
                                <td>Mild Sweetness</td>
                            </tr>
                            </tbody>
                        </table>
                        <div data-label="CODE EXAMPLE" class="demo-code-preview">
                                <pre><code class="language-html">&lt;table class="table table-bordered table-striped table-responsive-stack"&gt;
  &lt;thead class="thead-dark"&gt;
    &lt;tr&gt;
      &lt;th&gt;Name&lt;/th&gt;
      &lt;th&gt;Color&lt;/th&gt;
      &lt;th&gt;Taste&lt;/th&gt;
    &lt;/tr&gt;
  &lt;/thead&gt;
  &lt;tbody&gt;
    &lt;tr&gt;
      &lt;td&gt;Apple&lt;/td&gt;
      &lt;td&gt;Green and Red&lt;/td&gt;
      &lt;td&gt;Sweet and Tart&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
      &lt;td&gt;Banana&lt;/td&gt;
      &lt;td&gt;Yellow&lt;/td&gt;
      &lt;td&gt;Sweet and Mushy&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
      &lt;td&gt;Mango&lt;/td&gt;
      &lt;td&gt;Yellowish Green&lt;/td&gt;
      &lt;td&gt;Tangy&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
      &lt;td&gt;Orange&lt;/td&gt;
      &lt;td&gt;Orange&lt;/td&gt;
      &lt;td&gt;Sweet and Tangy&lt;/td&gt;
    &lt;/tr&gt;
    &lt;tr&gt;
      &lt;td&gt;Blueberry&lt;/td&gt;
      &lt;td&gt;Blue&lt;/td&gt;
      &lt;td&gt;Mild Sweetness&lt;/td&gt;
    &lt;/tr&gt;
  &lt;/tbody&gt;
&lt;/table&gt;</code></pre>
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
