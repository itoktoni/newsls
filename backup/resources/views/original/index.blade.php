@extends('layouts.app')

@section('head')
    <!-- Datepicker -->
    <link rel="stylesheet" href="{{ url('vendors/datepicker/daterangepicker.css') }}">

    <!-- Vmap -->
    <link rel="stylesheet" href="{{ url('vendors/vmap/jqvmap.min.css') }}">
@endsection

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>CRM System</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">CRM System</li>
                </ol>
            </nav>
        </div>
    </div>
    <!-- end::page-header -->

    <!-- begin::page content -->
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-7">
                <div class="card bg-secondary-gradient">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <h6 class="card-title mb-3">Welcome back Anna!</h6>
                                <p>Get familiar with dashboard, here are some ways to get started.</p>
                                <ul class="mb-3">
                                    <li>Check some stats for your website bellow</li>
                                    <li>Sync content to other devices</li>
                                </ul>
                                <a href="#" class="btn bg-white">Learn More</a>
                            </div>
                            <div class="col-md-5">
                                <img src="{{ url('assets/media/svg/undraw_onboarding_o8mv.svg') }}" alt="..." class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between">
                            <h6 class="card-title">Total Sales By Org Unit</h6>
                            <div>
                                <div class="dropdown">
                                    <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                       aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </a>
                                    <span class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item">Report</a>
                                            <a href="#" class="dropdown-item">Download</a>
                                            <a href="#" class="dropdown-item">Close</a>
                                        </span>
                                </div>
                            </div>
                        </div>
                        <h2 class="mb-3 font-weight-bold">$469,453</h2>
                        <div class="progress mb-3" style="height: 10px">
                            <div class="progress-bar w-25 bg-secondary-gradient" role="progressbar"></div>
                            <div class="progress-bar w-50 bg-info-gradient" role="progressbar"></div>
                            <div class="progress-bar w-25 bg-warning-gradient" role="progressbar"></div>
                            <div class="progress-bar w-25 bg-success-gradient" role="progressbar"></div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <p class="mb-0">
                                    <span class="fa fa-circle text-danger mr-1"></span>
                                    Hair Tran.
                                </p>
                                <h5 class="mt-2 mb-0">25%</h5>
                            </div>
                            <div class="col">
                                <p class="mb-0">
                                    <span class="fa fa-circle text-info mr-1"></span>
                                    Aesthetic
                                </p>
                                <h5 class="mt-2 mb-0">50%</h5>
                            </div>
                            <div class="col">
                                <p class="mb-0">
                                    <span class="fa fa-circle text-warning mr-1"></span>
                                    Dental
                                </p>
                                <h5 class="mt-2 mb-0">15%</h5>
                            </div>
                            <div class="col">
                                <p class="mb-0">
                                    <span class="fa fa-circle text-success mr-1"></span>
                                    Other
                                </p>
                                <h5 class="mt-2 mb-0">20%</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">

            <div class="col-md-12">

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-3">Contacts</h6>
                                        <div class="d-flex d-sm-block d-lg-flex align-items-end">
                                            <h2 class="mb-0 mr-2 font-weight-bold">1.425</h2>
                                            <p class="small text-muted mb-0 line-height-20">
                                                <span class="text-success">+ 5%</span> than last week
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="avatar avatar-lg">
                                            <div class="avatar-title bg-success-bright text-success rounded-circle">
                                                <i class="fa fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-3">Calls</h6>
                                        <div class="d-flex d-sm-block d-lg-flex align-items-end">
                                            <h2 class="mb-0 mr-2 font-weight-bold">554</h2>
                                            <p class="small text-muted mb-0 line-height-20">
                                                <span class="text-danger">- 2%</span> than last week
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="avatar avatar-lg">
                                            <div class="avatar-title bg-warning-bright text-warning rounded-circle">
                                                <i class="fa fa-phone"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="card-title mb-3">Contact Sales</h6>
                                        <div class="d-flex d-sm-block d-lg-flex align-items-end">
                                            <h2 class="mb-0 mr-2 font-weight-bold">900</h2>
                                            <p class="small text-muted mb-0 line-height-20">
                                                <span class="text-success">+ 9%</span> than last week
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="avatar avatar-lg">
                                            <div class="avatar-title bg-info-bright text-info rounded-circle">
                                                <i class="fa fa-money"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex justify-content-between">
                                    <h6 class="card-title">Sales this Month</h6>
                                    <div>
                                        <a href="#" class="mr-3">
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                        <div class="dropdown">
                                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                               aria-expanded="false">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                            <span class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">Report</a>
                                                    <a href="#" class="dropdown-item">Download</a>
                                                    <a href="#" class="dropdown-item">Close</a>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div id="chart1"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title d-flex justify-content-between">
                                    <h6 class="card-title">Contacts Statuses</h6>
                                    <div>
                                        <a href="#" class="mr-3">
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                        <div class="dropdown">
                                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                               aria-expanded="false">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                            <span class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">Report</a>
                                                    <a href="#" class="dropdown-item">Download</a>
                                                    <a href="#" class="dropdown-item">Close</a>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div id="contacts-statuses"></div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="d-md-flex align-items-center justify-content">
                                    <div>
                                        <h5 class="mb-2">Sales Avarage</h5>
                                        <small class="text-success mb-1">
                                            <i class="fa fa-caret-up mr-1"></i> 10% Last week
                                        </small>
                                        <h1 class="mb-0 font-weight-bold">216</h1>
                                    </div>
                                    <div class="ml-5">
                                        <span class="bar-3">2,5,9,6,5,2,4,3,7,5</span>
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
                                <div class="card-title d-md-flex align-items-start justify-content-between">
                                    <h6 class="card-title">Your Most Recent Earnings</h6>
                                    <div class="reportrange btn btn-outline-light btn-sm mt-3 mt-md-0">
                                        <i class="ti-calendar mr-2"></i>
                                        <span class="text"></span>
                                        <i class="ti-angle-down ml-2"></i>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th class="text-center">Sales Count</th>
                                                    <th class="text-center">Gross Earnings</th>
                                                    <th class="text-center">Tax Withheld</th>
                                                    <th class="text-center">Net Earnings</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td>03/15/2018</td>
                                                    <td class="text-center">1,050</td>
                                                    <td class="text-success text-center">+ $32,580.00</td>
                                                    <td class="text-danger text-center">- $3,023.10</td>
                                                    <td class="text-right text-center">$28,670.90</td>
                                                    <td class="text-right">
                                                        <a href="#" data-toggle="tooltip" title="Detail">
                                                            <i class="fa fa-external-link"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>03/14/2018</td>
                                                    <td class="text-center">780</td>
                                                    <td class="text-success text-center">+ $30,065.10</td>
                                                    <td class="text-danger text-center">- $2,780.00</td>
                                                    <td class="text-right text-center">$26,930.40</td>
                                                    <td class="text-right">
                                                        <a href="#" data-toggle="tooltip" title="Detail">
                                                            <i class="fa fa-external-link"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>03/13/2018</td>
                                                    <td class="text-center">1.980</td>
                                                    <td class="text-success text-center">+ $30,065.10</td>
                                                    <td class="text-danger text-center">- $2,780.00</td>
                                                    <td class="text-right text-center">$26,930.40</td>
                                                    <td class="text-right">
                                                        <a href="#" data-toggle="tooltip" title="Detail">
                                                            <i class="fa fa-external-link"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>03/12/2018</td>
                                                    <td class="text-center">300</td>
                                                    <td class="text-success text-center">+ $30,065.10</td>
                                                    <td class="text-danger text-center">- $2,780.00</td>
                                                    <td class="text-right text-center">$26,930.40</td>
                                                    <td class="text-right">
                                                        <a href="#" data-toggle="tooltip" title="Detail">
                                                            <i class="fa fa-external-link"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>03/11/2018</td>
                                                    <td class="text-center">940</td>
                                                    <td class="text-success text-center">+ $30,065.10</td>
                                                    <td class="text-danger text-center">- $2,780.00</td>
                                                    <td class="text-right text-center">$26,930.40</td>
                                                    <td class="text-right">
                                                        <a href="#" data-toggle="tooltip" title="Detail">
                                                            <i class="fa fa-external-link"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mt-3">
                                    <div class="col-md-3">
                                        <div class="card mb-0">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="icon-block icon-block-floating mr-3 icon-block-lg icon-block-outline-success text-success">
                                                        <i class="fa fa-bar-chart"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-uppercase font-size-11">Gross Earnings</h6>
                                                        <h4 class="mb-0 font-weight-bold">$1.958,104</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mb-0">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="icon-block icon-block-floating mr-3 icon-block-lg icon-block-outline-danger  text-danger">
                                                        <i class="fa fa-hand-lizard-o"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-uppercase font-size-11">Tax Withheld</h6>
                                                        <h4 class="mb-0 font-weight-bold">$234,769</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card mb-0">
                                            <div class="card-body p-3">
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="icon-block icon-block-floating mr-3 icon-block-lg icon-block-outline-warning text-warning">
                                                        <i class="fa fa-dollar"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="text-uppercase font-size-11">Net Earnings</h6>
                                                        <h4 class="mb-0 font-weight-bold">$1.608,469</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-xl-6 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title d-flex justify-content-between">
                                    <span>Income Distribution</span>
                                    <span class="dropdown">
                                            <a class="btn btn-outline-light btn-sm dropdown-toggle" href="#"
                                               data-toggle="dropdown">USA</a>
                                            <span class="dropdown-menu dropdown-menu-right">
                                                <a href="#" class="dropdown-item">USA</a>
                                                <a href="#" class="dropdown-item">Germany</a>
                                                <a href="#" class="dropdown-item">France</a>
                                                <a href="#" class="dropdown-item">Italy</a>
                                            </span>
                                        </span>
                                </h6>
                                <div id="vmap_usa_en" style="height: 300px"></div>
                                <div class="table-responsive mt-5">
                                    <table class="table table-borderless table-striped mb-0">
                                        <thead>
                                        <tr>
                                            <th class="wd-40">States</th>
                                            <th class="wd-25 text-center">Orders</th>
                                            <th class="wd-35 text-center">Earnings</th>
                                            <th class="wd-35"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td>California</td>
                                            <td class="text-center">12,201</td>
                                            <td class="text-center text-success">$150,200.80</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Texas</td>
                                            <td class="text-center">11,950</td>
                                            <td class="text-center text-success">$138,910.20</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Wyoming</td>
                                            <td class="text-center">11,198</td>
                                            <td class="text-center text-danger">$132,050.00</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Florida</td>
                                            <td class="text-center">9,885</td>
                                            <td class="text-center text-success">$127,762.10</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>New York</td>
                                            <td class="text-center">21,198</td>
                                            <td class="text-center text-danger">$432,410.00</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Montana</td>
                                            <td class="text-center">2,885</td>
                                            <td class="text-center text-success">$7,100.00</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Arkansas</td>
                                            <td class="text-center">1,298</td>
                                            <td class="text-center text-success">$2,310.50</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Kansas</td>
                                            <td class="text-center">5,900</td>
                                            <td class="text-center text-danger">$97,500.00</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Colorado</td>
                                            <td class="text-center">15,900</td>
                                            <td class="text-center text-danger">$397,371.32</td>
                                            <td class="text-right">
                                                <a href="#" data-toggle="tooltip" title="Detail">
                                                    <i class="fa fa-external-link"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <h6 class="card-title">Daily Task List</h6>
                                    <div>
                                        <a href="#" class="mr-3">
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                        <div class="dropdown">
                                            <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                               aria-expanded="false">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                            <span class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">Report</a>
                                                    <a href="#" class="dropdown-item">Download</a>
                                                    <a href="#" class="dropdown-item">Close</a>
                                                </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="custom-control custom-checkbox-success custom-checkbox todo-item">
                                    <input type="checkbox" class="custom-control-input" id="customCheck1">
                                    <label class="custom-control-label d-flex justify-content-between"
                                           for="customCheck1">Talk
                                        to new customers
                                        <span class="text-muted">13 May 2019</span>
                                    </label>
                                </div>
                                <div
                                    class="custom-control custom-checkbox-success custom-checkbox-success custom-checkbox todo-item">
                                    <input type="checkbox" class="custom-control-input" id="customCheck2" checked>
                                    <label class="custom-control-label d-flex justify-content-between"
                                           for="customCheck2">Older
                                        users will be deleted from
                                        the system
                                        <span class="text-muted">20 Apr 2019</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox-success custom-checkbox todo-item">
                                    <input type="checkbox" class="custom-control-input" id="customCheck3">
                                    <label class="custom-control-label d-flex justify-content-between"
                                           for="customCheck3">Assignment
                                        will be
                                        completed
                                        <span class="text-muted">13 May 2019</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox-success custom-checkbox todo-item">
                                    <input type="checkbox" class="custom-control-input" id="customCheck4" checked>
                                    <label class="custom-control-label d-flex justify-content-between"
                                           for="customCheck4">Customer
                                        notes
                                        <span class="text-muted">10 Jan 2018</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox-success custom-checkbox todo-item">
                                    <input type="checkbox" class="custom-control-input" id="customCheck5">
                                    <label class="custom-control-label d-flex justify-content-between"
                                           for="customCheck5">Clear
                                        old log records and backup
                                        will be taken
                                        <span class="text-muted">13 May 2019</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox custom-checkbox-success todo-item">
                                    <input type="checkbox" class="custom-control-input" id="customCheck6">
                                    <label class="custom-control-label d-flex justify-content-between"
                                           for="customCheck6">Talk
                                        to new customers
                                        <span class="text-muted">27 Feb 2019</span>
                                    </label>
                                </div>
                                <div class="custom-control custom-checkbox-success custom-checkbox todo-item">
                                    <input type="checkbox" class="custom-control-input" id="customCheck7" checked>
                                    <label class="custom-control-label d-flex justify-content-between"
                                           for="customCheck7">Older
                                        users will be deleted from
                                        the system
                                        <span class="text-muted">13 May 2019</span>
                                    </label>
                                </div>
                                <form class="mt-4">
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                               aria-label="Example text with button addon"
                                               placeholder="New task" aria-describedby="button-addon1">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button" id="button-addon1">Add
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Users Assigned to Me</h6>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex align-items-center p-l-r-0">
                                        <div>
                                            <figure class="avatar avatar-state-success m-r-15">
                                                <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}"
                                                     class="rounded-circle" alt="image">
                                            </figure>
                                        </div>
                                        <div>
                                            <h6 class="m-b-0">Valentine Maton</h6>
                                            <small class="text-muted">Engineer</small>
                                        </div>
                                        <div class="ml-auto">
                                            <span class="badge badge-danger mr-2 d-sm-inline d-none">Denied</span>
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-outline-light btn-sm"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                    <a href="#" class="dropdown-item">Assign</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center p-l-r-0">
                                        <div>
                                            <figure class="avatar avatar-state-success m-r-15">
                                                <img src="{{ url('assets/media/image/user/women_avatar3.jpg') }}"
                                                     class="rounded-circle" alt="image">
                                            </figure>
                                        </div>
                                        <div>
                                            <h6 class="m-b-0">Holmes Cherryman</h6>
                                            <small class="text-muted">Human resources</small>
                                        </div>
                                        <div class="ml-auto">
                                            <span class="badge badge-success mr-2 d-sm-inline d-none">Completed</span>
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-outline-light btn-sm"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                    <a href="#" class="dropdown-item">Assign</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center p-l-r-0">
                                        <div>
                                            <figure class="avatar avatar-state-success m-r-15">
                                                <span class="avatar-title bg-secondary rounded-circle">MH</span>
                                            </figure>
                                        </div>
                                        <div>
                                            <h6 class="m-b-0">Malanie Hanvey</h6>
                                            <small class="text-muted">Real estate agent</small>
                                        </div>
                                        <div class="ml-auto">
                                            <span class="badge badge-warning mr-2 d-sm-inline d-none">Pending</span>
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-outline-light btn-sm"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                    <a href="#" class="dropdown-item">Assign</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="list-group-item d-flex align-items-center p-l-r-0">
                                        <div>
                                            <figure class="avatar avatar-state-success m-r-15">
                                                <img src="{{ url('assets/media/image/user/women_avatar1.jpg') }}"
                                                     class="rounded-circle" alt="image">
                                            </figure>
                                        </div>
                                        <div>
                                            <h6 class="m-b-0">Kenneth Hune</h6>
                                            <small class="text-muted">Engineer</small>
                                        </div>
                                        <div class="ml-auto">
                                            <span class="badge badge-danger mr-2 d-sm-inline d-none">Denied</span>
                                            <div class="dropdown">
                                                <a href="#" data-toggle="dropdown"
                                                   class="btn btn-outline-light btn-sm"
                                                   aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a href="#" class="dropdown-item">View</a>
                                                    <a href="#" class="dropdown-item">Send Message</a>
                                                    <a href="#" class="dropdown-item">Assign</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                                <div class="mt-3 text-center">
                                    <a href="#">
                                        View All
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end::page content -->

@endsection

@section('script')
    <!-- Chartjs -->
    <script src="{{ url('vendors/charts/chartjs/chart.min.js') }}"></script>

    <!-- Apex chart -->
    <script src="{{ url('vendors/charts/apex/apexcharts.min.js') }}"></script>

    <!-- Circle progress -->
    <script src="{{ url('vendors/circle-progress/circle-progress.min.js') }}"></script>

    <!-- Peity -->
    <script src="{{ url('vendors/charts/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ url('assets/js/examples/charts/peity.js') }}"></script>

    <!-- Datepicker -->
    <script src="{{ url('vendors/datepicker/daterangepicker.js') }}"></script>

    <!-- Vamp -->
    <script src="{{ url('vendors/vmap/jquery.vmap.min.js') }}"></script>
    <script src="{{ url('vendors/vmap/maps/jquery.vmap.usa.js') }}"></script>
    <script src="{{ url('assets/js/examples/vmap.js') }}"></script>

    <!-- Dashboard scripts -->
    <script src="{{ url('assets/js/examples/dashboard.js') }}"></script>
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
