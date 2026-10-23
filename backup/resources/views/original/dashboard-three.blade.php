@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Analytics</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Analytics</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold mb-2">3.605</h2>
                                <div>Click Through</div>
                            </div>
                            <div>
                                <span class="dashboard-pie-1">2/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold mb-2">3.137</h2>
                                <div>View Through</div>
                            </div>
                            <div>
                                <span class="dashboard-pie-2">4/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold mb-2">8.765</h2>
                                <div>Total Conversions</div>
                            </div>
                            <div>
                                <span class="dashboard-pie-3">1/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="font-weight-bold mb-2">68%</h2>
                                <div>Page Retention Rate</div>
                            </div>
                            <div>
                                <span class="dashboard-pie-4">2/5</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title">Website Audience Metrics</h6>
                            <div>
                                <a href="#" class="mr-3">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Report</a>
                                        <a href="#" class="dropdown-item">Download</a>
                                        <a href="#" class="dropdown-item">Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-lg-none d-sm-block mb-4"></div>
                        <div id="users-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4 col-md-12">

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Sessions By Channel</h6>
                        <div class="mb-4">
                            <canvas id="chart_demo_7"></canvas>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p class="mb-2">Organic Search</p>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-circle text-danger mr-1 font-size-11"></i>
                                    <h4 class="mb-0 font-weight-bold">1.242</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <p class="mb-2">Email</p>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-circle text-info mr-1 font-size-11"></i>
                                    <h4 class="mb-0 font-weight-bold">742</h4>
                                </div>
                            </div>
                            <div class="col-6 m-t-20">
                                <p class="mb-2">Referrral</p>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-circle text-warning mr-1 font-size-11"></i>
                                    <h4 class="mb-0 font-weight-bold">442</h4>
                                </div>
                            </div>
                            <div class="col-6 m-t-20">
                                <p class="mb-2">Social Media</p>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-circle text-success mr-1 font-size-11"></i>
                                    <h4 class="mb-0 font-weight-bold">1.742</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Top Traffic Source</h6>
                        <div class="text-center">
                            <h1 class="font-size-40 font-weight-bold mb-3">30.201</h1>
                            <p>Organic Search</p>
                            <p>Measures your user's sources that generate traffic
                                metrics
                                to your website for
                                this month.</p>
                            <div class="row mb-4">
                                <div class="col-6">
                                    <h2 class="text-success font-weight-bold">%20</h2>
                                    <div>Desktop</div>
                                </div>
                                <div class="col-6">
                                    <h2 class="text-warning font-weight-bold">%80</h2>
                                    <div>Mobile</div>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary">Learn More</button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Website Audience Metrics</h6>
                        <div class="mb-5">
                            <p class="d-flex justify-content-between">
                                <span class="">New Users</span>
                                <span class="text-muted">65% goal reached</span>
                            </p>
                            <h4 class="font-weight-bold">13,596</h4>
                            <div class="progress" style="height: 5px">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 42%;"
                                     aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <p class="d-flex justify-content-between">
                                <span>Page Views</span>
                                <span class="text-muted">45% goal reached</span>
                            </p>
                            <h4 class="font-weight-bold">43,596</h4>
                            <div class="progress" style="height: 5px">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 42%;"
                                     aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <p class="d-flex justify-content-between">
                                <span>Page Sessions</span>
                                <span class="text-muted">20% goal reached</span>
                            </p>
                            <h4 class="font-weight-bold">16,869</h4>
                            <div class="progress" style="height: 5px">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 42%;"
                                     aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div>
                            <p class="d-flex justify-content-between">
                                <span>Bounce Rate</span>
                                <span class="text-muted">85% goal reached</span>
                            </p>
                            <h4 class="font-weight-bold">28.50%</h4>
                            <div class="progress" style="height: 5px">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 42%;"
                                     aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-8 col-md-12">

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title">Device Sessions</h6>
                            <div>
                                <a href="#" class="mr-3">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Report</a>
                                        <a href="#" class="dropdown-item">Download</a>
                                        <a href="#" class="dropdown-item">Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-2 col-md-4 col-sm-12">
                                <div class="d-flex">
                                    <i class="fa fa-circle text-primary mr-2 mt-1 font-size-11"></i>
                                    <div>
                                        <h4>1.902</h4>
                                        <div class="text-muted">Mobile</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12">
                                <div class="d-flex">
                                    <i class="fa fa-circle text-success mr-2 mt-1 font-size-11"></i>
                                    <div>
                                        <h4>1.202</h4>
                                        <div class="text-muted">Desktop</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12">
                                <div class="d-flex">
                                    <i class="fa fa-circle text-warning mr-2 mt-1 font-size-11"></i>
                                    <div>
                                        <h4>365</h4>
                                        <div class="text-muted">Other</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="device_session_chart"></div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title">Total Visits</h6>
                            <div>
                                <a href="#" class="mr-3">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Report</a>
                                        <a href="#" class="dropdown-item">Download</a>
                                        <a href="#" class="dropdown-item">Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr class="text-uppercase font-size-11 text-muted">
                                    <th>Link</th>
                                    <th>Page Title</th>
                                    <th>Percentage (%)</th>
                                    <th class="text-right">Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <a href="#">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td>Homepage</td>
                                    <td>
                                        <div class="progress" style="height: 5px">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: 65%;"
                                                 aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-right">65.35%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td>About</td>
                                    <td>
                                        <div class="progress" style="height: 5px">
                                            <div class="progress-bar bg-warning" role="progressbar"
                                                 style="width: 51%;"
                                                 aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-right">51.20%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td>Products</td>
                                    <td>
                                        <div class="progress" style="height: 5px">
                                            <div class="progress-bar bg-danger" role="progressbar"
                                                 style="width: 39%;"
                                                 aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-right">39.10%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td>Categories</td>
                                    <td>
                                        <div class="progress" style="height: 5px">
                                            <div class="progress-bar bg-primary" role="progressbar"
                                                 style="width: 40%;"
                                                 aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-right">40%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">
                                            <i class="fa fa-external-link"></i>
                                        </a>
                                    </td>
                                    <td>Contact</td>
                                    <td>
                                        <div class="progress" style="height: 5px">
                                            <div class="progress-bar bg-success" role="progressbar"
                                                 style="width: 80%;"
                                                 aria-valuenow="42" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td class="text-right">80%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title">Browser Used By Users</h6>
                            <div>
                                <a href="#" class="mr-3">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="#" class="dropdown-item">Report</a>
                                        <a href="#" class="dropdown-item">Download</a>
                                        <a href="#" class="dropdown-item">Close</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Browser</th>
                                    <th>Sessions</th>
                                    <th>Bounce Rate</th>
                                    <th class="text-right">Conversion Rate</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <i class="fa fa-chrome"></i>
                                    </td>
                                    <td>Google Chrome</td>
                                    <td>13,41</td>
                                    <td>40.95%</td>
                                    <td class="text-right">19.45%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-firefox"></i>
                                    </td>
                                    <td>Firefox</td>
                                    <td>13,41</td>
                                    <td>40.95%</td>
                                    <td class="text-right">19.45%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-edge"></i>
                                    </td>
                                    <td>Edge</td>
                                    <td>13,41</td>
                                    <td>40.95%</td>
                                    <td class="text-right">19.45%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-safari"></i>
                                    </td>
                                    <td>Safari</td>
                                    <td>13,41</td>
                                    <td>40.95%</td>
                                    <td class="text-right">19.45%</td>
                                </tr>
                                <tr>
                                    <td>
                                        <i class="fa fa-opera"></i>
                                    </td>
                                    <td>Opera</td>
                                    <td>13,41</td>
                                    <td>40.95%</td>
                                    <td class="text-right">19.45%</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@section('script')

    <!-- Apex chart -->
    <script src="{{ url('/vendors/charts/apex/apexcharts.min.js') }}"></script>

    <!-- Chartjs -->
    <script src="{{ url('/vendors/charts/chartjs/chart.min.js') }}"></script>

    <!-- Circle progress -->
    <script src="{{ url('/vendors/circle-progress/circle-progress.min.js') }}"></script>

    <!-- Datepicker -->
    <script src="{{ url('/vendors/datepicker/daterangepicker.js') }}"></script>

    <!-- Peity -->
    <script src="{{ url('/vendors/charts/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ url('/assets/js/examples/charts/peity.js') }}"></script>

    <!-- Dashboard scripts -->
    <script src="{{ url('/assets/js/examples/dashboard.js') }}"></script>
    <div class="colors"> <!-- To use theme colors with Javascript -->
        <div class="bg-primary"></div>
        <div class="bg-primary-bright"></div>
        <div class="bg-secondary"></div>
        <div class="bg-secondary-bright"></div>
        <div class="bg-info"></div>
        <div class="bg-info-bright"></div>
        <div class="bg-success"></div>
        <div class="bg-success-bright"></div>
        <div class="bg-danger"></div>
        <div class="bg-danger-bright"></div>
        <div class="bg-warning"></div>
        <div class="bg-warning-bright"></div>
    </div>

@endsection
