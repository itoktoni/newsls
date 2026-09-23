@extends('layouts.app')

@section('content')

    <!-- begin::page-header -->
    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Pricing Tables</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="#">Pages</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Pricing Tables</li>
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
                            <table class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col" class="text-center p-t-b-40">
                                        <div class="m-b-10 text-uppercase font-size-11 opacity-7">Starter</div>
                                        <div class="m-b-0 m-l-r-30 font-size-35 font-weight-bold">
                                            Free
                                        </div>
                                    </th>
                                    <th scope="col" class="text-center p-t-b-40">
                                        <div class="m-b-10 text-uppercase font-size-11 opacity-7">Basic</div>
                                        <div class="m-b-0 m-l-r-30 font-weight-bold">
                                            <sup>
                                                <small>$</small>
                                            </sup>
                                            <span class="font-size-35">189</span>
                                            <sup>
                                                <small class="font-size-11">monthly</small>
                                            </sup>
                                        </div>
                                    </th>
                                    <th scope="col" class="text-center p-t-b-40">
                                        <div class="m-b-10 text-uppercase font-size-11 opacity-7">Premium</div>
                                        <div class="m-b-0 m-l-r-30 font-weight-bold">
                                            <sup>
                                                <small>$</small>
                                            </sup>
                                            <span class="font-size-35">289</span>
                                            <sup>
                                                <small class="font-size-11">monthly</small>
                                            </sup>
                                        </div>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr class="text-center">
                                    <td>Email preview on air</td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>Spam testing and blocking</td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>10 GB Space</td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>50 user accounts</td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>Free support for one years</td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>Free upgrade for one year</td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                </tr>
                                <tr class="text-center">
                                    <td>Free upgrade for one year</td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="x" class="width-18 height-18 text-danger"></i>
                                    </td>
                                    <td>
                                        <i data-feather="check" class="width-18 height-18 text-success"></i>
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td class="text-center p-t-b-30">
                                        <button class="btn btn-primary">Start Trial</button>
                                    </td>
                                    <td class="text-center p-t-b-30">
                                        <button class="btn btn-primary">Start Trial</button>
                                    </td>
                                    <td class="text-center p-t-b-30">
                                        <button class="btn btn-primary">Start Trial</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="pricing-table m-b-20">
                                    <h6 class="m-b-20 text-uppercase font-size-11 text-center">Starter</h6>
                                    <h1 class="font-weight-bold text-center font-size-35">
                                        Free
                                    </h1>
                                    <ul class="m-b-30 m-t-30 list-group list-group-flush">
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            Email preview on air
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            Spam testing and blocking
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-danger icon-block-xs icon-block-floating">
                                                    <i data-feather="x" class="width-18 height-18"></i>
                                                </span>
                                            10 GB Space
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-danger icon-block-xs icon-block-floating">
                                                    <i data-feather="x" class="width-18 height-18"></i>
                                                </span>
                                            50 user accounts
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-danger icon-block-xs icon-block-floating">
                                                    <i data-feather="x" class="width-18 height-18"></i>
                                                </span>
                                            Free support for one years
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-danger icon-block-xs icon-block-floating">
                                                    <i data-feather="x" class="width-18 height-18"></i>
                                                </span>
                                            Free upgrade for one year
                                        </li>
                                    </ul>
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-primary">Start Trial</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success-bright">
                            <div class="card-body">
                                <div class="pricing-table selected-pricing-table m-b-20">
                                    <h6 class="m-b-20 text-uppercase font-size-11 text-center">Basic</h6>
                                    <h1 class="m-b-20 font-weight-bold text-center">
                                        <sup>
                                            <small>$</small>
                                        </sup>
                                        <span class="font-size-35">189</span>
                                        <sup>
                                            <small class="font-size-11 text-uppercase">yearly</small>
                                        </sup>
                                    </h1>
                                    <ul class="m-b-30 m-t-30 list-group list-group-flush">
                                        <li class="list-group-item bg-none d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            Email preview on air
                                        </li>
                                        <li class="list-group-item bg-none d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            Spam testing and blocking
                                        </li>
                                        <li class="list-group-item bg-none d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            10 GB Space
                                        </li>
                                        <li class="list-group-item bg-none d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            50 user accounts
                                        </li>
                                        <li class="list-group-item bg-none d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-danger icon-block-xs icon-block-floating">
                                                    <i data-feather="x" class="width-18 height-18"></i>
                                                </span>
                                            Free support for one years
                                        </li>
                                        <li class="list-group-item bg-none d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-danger icon-block-xs icon-block-floating">
                                                    <i data-feather="x" class="width-18 height-18"></i>
                                                </span>
                                            Free upgrade for one year
                                        </li>
                                    </ul>
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-primary">Start Trial</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="pricing-table m-b-20">
                                    <h6 class="m-b-20 text-uppercase font-size-11 text-center">Premium</h6>
                                    <h1 class="m-b-20 font-weight-bold text-center">
                                        <sup>
                                            <small>$</small>
                                        </sup>
                                        <span class="font-size-35">289</span>
                                        <sup>
                                            <small class="text-muted font-size-11 text-uppercase">monthly</small>
                                        </sup>
                                    </h1>
                                    <ul class="m-b-30 m-t-30 list-group list-group-flush">
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            Email preview on air
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            Spam testing and blocking
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            10 GB Space
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            50 user accounts
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-success icon-block-xs icon-block-floating">
                                                    <i data-feather="check" class="width-18 height-18"></i>
                                                </span>
                                            Free support for one years
                                        </li>
                                        <li class="list-group-item d-flex align-items-center">
                                                <span class="icon-block mr-3 bg-danger icon-block-xs icon-block-floating">
                                                    <i data-feather="x" class="width-18 height-18"></i>
                                                </span>
                                            Free upgrade for one year
                                        </li>
                                    </ul>
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-primary">Start Trial</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
