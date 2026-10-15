@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="row">
            <div class="col-md-12">

                <div class="card">
                    <div class="card-body p-50">
                        <div class="invoice">
                            <div class="d-md-flex justify-content-between align-items-center">
                                <h2 class="font-weight-800 d-flex align-items-center">
                                    <img class="m-r-20" src="{{ url('assets/media/image/logo.png') }}" alt="image">
                                </h2>
                                <h3 class="text-xs-left m-b-0">Invoice #INV-16</h3>
                            </div>
                            <hr class="m-t-b-50">
                            <div class="row">
                                <div class="col-md-6">
                                    <p>
                                        <b>Bordash Admin Dashboard Template</b>
                                    </p>
                                    <p>104,<br>Minare SK,<br>Canada, K1A 0G9.</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-right">
                                        <b>Invoice to</b>
                                    </p>
                                    <p class="text-right">Gaala &amp; Sons,<br> C-201, Beykoz-34800,<br> Canada, K1A 0G9.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table mb-4 mt-4">
                                    <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit cost</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="text-right">
                                        <td class="text-left">1</td>
                                        <td class="text-left">Brochure Design</td>
                                        <td>2</td>
                                        <td>$20</td>
                                        <td>$40</td>
                                    </tr>
                                    <tr class="text-right">
                                        <td class="text-left">2</td>
                                        <td class="text-left">Web Design Packages(Template) - Basic</td>
                                        <td>05</td>
                                        <td>$25</td>
                                        <td>$125</td>
                                    </tr>
                                    <tr class="text-right">
                                        <td class="text-left">3</td>
                                        <td class="text-left">Print Ad - Basic - Color</td>
                                        <td>08</td>
                                        <td>$500</td>
                                        <td>$4000</td>
                                    </tr>
                                    <tr class="text-right">
                                        <td class="text-left">4</td>
                                        <td class="text-left">Down Coat</td>
                                        <td>1</td>
                                        <td>$5</td>
                                        <td>$5</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <p>Sub - Total amount: $12,348</p>
                                <p>vat (10%) : $138</p>
                                <h4 class="font-weight-800">Total : $13,986</h4>
                            </div>
                            <p class="text-center small text-muted  m-t-50">
                        <span class="row">
                            <span class="col-md-6 offset-3">
                                Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab, at.
                            </span>
                        </span>
                            </p>
                        </div>
                        <div class="text-right d-print-none">
                            <hr class="mb-5 mt-5">
                            <a href="#" class="btn btn-primary">
                                <i data-feather="send" class="mr-2"></i> Send Invoice
                            </a>
                            <a href="javascript:window.print()" class="btn btn-success m-l-5">
                                <i data-feather="printer" class="mr-2"></i> Print
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
