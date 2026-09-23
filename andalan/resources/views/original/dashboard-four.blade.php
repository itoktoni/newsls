@extends('layouts.app')

@section('content')

    <div class="page-header">
        <div class="container-fluid d-sm-flex justify-content-between">
            <h4>Project Management</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Project Management</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container-fluid">

        <div class="row">
            <div class="col-lg-6 col-md-12">

                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title">Growth</h6>
                                <h2>$2,450</h2>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="progress flex-grow-1" style="height: 5px">
                                    <div class="progress-bar bg-primary" role="progressbar"
                                         style="width: 62%;"
                                         aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="ml-2">%62</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title">Project</h6>
                                <h2>2,320</h2>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="progress flex-grow-1" style="height: 5px">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                         style="width:73%;"
                                         aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="ml-2">%73</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title">Income</h6>
                                <h2>$9,750</h2>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="progress flex-grow-1" style="height: 5px">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: 40%;"
                                         aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="ml-2">%40</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-body">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-title">Employers</h6>
                                <h2>3,156</h2>
                            </div>
                            <div class="d-flex align-items-center mt-2">
                                <div class="progress flex-grow-1" style="height: 5px">
                                    <div class="progress-bar bg-info" role="progressbar"
                                         style="width: 55%;"
                                         aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="ml-2">%55</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body text-center">
                        <h5>10th Dance Competition 2019</h5>
                        <p class="text-muted">Sunt in culpa qui officia deserunt mol excep teur sint occa ecat cupi
                            datat non</p>
                        <div class="mb-4 d-flex align-items-center justify-content-center">
                            <div class="avatar-group">
                                <figure class="avatar">
                                    <span class="avatar-title bg-success rounded-circle">E</span>
                                </figure>
                                <figure class="avatar">
                                    <img src="{{ url('assets/media/image/user/women_avatar4.jpg') }}" class="rounded-circle"
                                         alt="avatar">
                                </figure>
                                <figure class="avatar">
                                    <span class="avatar-title bg-danger rounded-circle">S</span>
                                </figure>
                                <figure class="avatar">
                                    <img src="{{ url('assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle"
                                         alt="avatar">
                                </figure>
                                <figure class="avatar">
                                    <span class="avatar-title bg-primary rounded-circle">C</span>
                                </figure>
                            </div>
                            <small class="text-muted ml-2">10+ friends are coming</small>
                        </div>
                        <div class="clearfix"></div>
                        <a href="" class="btn btn-outline-primary btn-sm">View All</a>
                    </div>
                </div>

            </div>
            <div class="col-lg-6 col-md-12">

                <div class="card">
                    <div class="card-body">
                        <div class="card-title d-flex justify-content-between">
                            <h6 class="card-title">Project Tasks</h6>
                            <div>
                                <a href="#" class="mr-3">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <span class="dropdown">
                                        <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                           aria-expanded="false">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <span class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item">Action</a>
                                            <a href="#" class="dropdown-item">Another action</a>
                                            <a href="#" class="dropdown-item">Something else here</a>
                                        </span>
                                    </span>
                            </div>
                        </div>
                        <div id="project-tasks"></div>
                    </div>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">
                                <a href="" class="link-2">Frontend Development</a>
                                <span class="badge badge-success ml-2">Active</span>
                            </h5>
                            <div class="dropdown ml-auto">
                                <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Share</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                    <a href="#" class="dropdown-item">Copy to</a>
                                    <a href="#" class="dropdown-item">Rename</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted small mt-1 mb-3">10 opened tasks, 5 tasks completed</div>
                        <div class="progress mb-2" style="height: 5px;">
                            <div class="progress-bar bg-primary" style="width: 53%;"></div>
                        </div>
                        <p class="small">
                            <strong>53%</strong> completed
                        </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ac malesuada nisl.
                            Maecenas quis ultrices tellus.</p>
                        <div class="row">
                            <div class="col">
                                <div class="text-muted mb-1 small">Created</div>
                                <div>02/01/2019</div>
                            </div>
                            <div class="col">
                                <div class="text-muted mb-1 small">Deadline</div>
                                <div>03/12/2019</div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                    <div class="card-body">
                        <div class="small mb-2">Team Member</div>
                        <div class="avatar-group">
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/women_avatar4.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/man_avatar3.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">
                                <a href="" class="link-2">Backend Development</a>
                                <span class="badge badge-warning ml-2">Pending</span>
                            </h5>
                            <div class="dropdown ml-auto">
                                <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Share</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                    <a href="#" class="dropdown-item">Copy to</a>
                                    <a href="#" class="dropdown-item">Rename</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted small mt-1 mb-3">10 opened tasks, 5 tasks completed</div>
                        <div class="progress mb-2" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: 53%;"></div>
                        </div>
                        <p class="small">
                            <strong>53%</strong> completed
                        </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ac malesuada nisl.
                            Maecenas quis ultrices tellus.</p>
                        <div class="row">
                            <div class="col">
                                <div class="text-muted mb-1 small">Created</div>
                                <div>02/01/2019</div>
                            </div>
                            <div class="col">
                                <div class="text-muted mb-1 small">Deadline</div>
                                <div>03/12/2019</div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                    <div class="card-body">
                        <div class="small mb-2">Team Member</div>
                        <div class="avatar-group">
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/women_avatar5.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/women_avatar1.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0">
                                <a href="" class="link-2">UI-Kit Development</a>
                                <span class="badge badge-success ml-2">Active</span>
                            </h5>
                            <div class="dropdown ml-auto">
                                <a href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item">View Detail</a>
                                    <a href="#" class="dropdown-item">Share</a>
                                    <a href="#" class="dropdown-item">Download</a>
                                    <a href="#" class="dropdown-item">Copy to</a>
                                    <a href="#" class="dropdown-item">Rename</a>
                                    <a href="#" class="dropdown-item text-danger">Delete</a>
                                </div>
                            </div>
                        </div>
                        <div class="text-muted small mt-1 mb-3">10 opened tasks, 5 tasks completed</div>
                        <div class="progress mb-2" style="height: 5px;">
                            <div class="progress-bar bg-success" style="width: 53%;"></div>
                        </div>
                        <p class="small">
                            <strong>53%</strong> completed
                        </p>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque ac malesuada nisl.
                            Maecenas quis ultrices tellus.</p>
                        <div class="row">
                            <div class="col">
                                <div class="text-muted mb-1 small">Created</div>
                                <div>02/01/2019</div>
                            </div>
                            <div class="col">
                                <div class="text-muted mb-1 small">Deadline</div>
                                <div>03/12/2019</div>
                            </div>
                        </div>
                    </div>
                    <hr class="m-0">
                    <div class="card-body">
                        <div class="small mb-2">Team Member</div>
                        <div class="avatar-group">
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/women_avatar2.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/women_avatar4.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/man_avatar3.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/man_avatar1.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                            <figure class="avatar avatar-sm">
                                <img src="{{ url('assets/media/image/user/man_avatar2.jpg') }}" class="rounded-circle"
                                     alt="avatar">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title">Recent Projects</h6>
                            <div>
                                <a href="#" class="mr-3">
                                    <i class="fa fa-refresh"></i>
                                </a>
                                <span class="dropdown">
                                        <a href="#" data-toggle="dropdown" aria-haspopup="true"
                                           aria-expanded="false">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <span class="dropdown-menu dropdown-menu-right">
                                            <a href="#" class="dropdown-item">Action</a>
                                            <a href="#" class="dropdown-item">Another action</a>
                                            <a href="#" class="dropdown-item">Something else here</a>
                                        </span>
                                    </span>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Project</th>
                                    <th class="text-center">Task</th>
                                    <th class="text-center">Members</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-right">Progress</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <a href="#">Frontend Development</a>
                                    </td>
                                    <td class="text-center">25</td>
                                    <td class="text-center">
                                        <div class="avatar-group">
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar2.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar4.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/man_avatar3.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/man_avatar1.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">In Progress</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-info" style="width: 53%;"></div>
                                            </div>
                                            <small class="ml-2">%53</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">Backend Development</a>
                                    </td>
                                    <td class="text-center">10</td>
                                    <td class="text-center">
                                        <div class="avatar-group">
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar2.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar4.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/man_avatar3.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/man_avatar1.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/man_avatar5.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/man_avatar2.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-warning">Pending</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-warning" style="width: 80%;"></div>
                                            </div>
                                            <small class="ml-2">%80</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">UI-Kit Development</a>
                                    </td>
                                    <td class="text-center">32</td>
                                    <td class="text-center">
                                        <div class="avatar-group">
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar2.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar4.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">Active</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-success" style="width: 35%;"></div>
                                            </div>
                                            <small class="ml-2">%35</small>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <a href="#">UI-Kit Development 2</a>
                                    </td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">
                                        <div class="avatar-group">
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar1.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar3.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                            <figure class="avatar avatar-sm">
                                                <img src="{{ url('/assets/media/image/user/women_avatar2.jpg') }}"
                                                     class="rounded-circle"
                                                     alt="avatar">
                                            </figure>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-info">In Progress</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1" style="height: 5px;">
                                                <div class="progress-bar bg-info" style="width: 50%;"></div>
                                            </div>
                                            <small class="ml-2">%50</small>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="card-title">Upcoming Meeting</h6>
                            <a href="#">View All</a>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="text-center">
                                <div class="avatar">
                                    <span
                                        class="avatar-title bg-info-bright text-info rounded-circle font-size-22">17</span>
                                </div>
                            </div>
                            <div class="m-l-20">
                                <h5 class="mb-2">
                                    <a class="text-dark">UI Discussion</a>
                                </h5>
                                <p class="mb-0">Execute core that as result.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="text-center">
                                <div class="avatar">
                                    <span class="avatar-title bg-danger-bright text-danger rounded-circle font-size-22">21</span>
                                </div>
                            </div>
                            <div class="m-l-20">
                                <h5 class="mb-2">
                                    <a class="text-dark">Project Schdule</a>
                                </h5>
                                <p class="mb-0">Special cloth alert always.</p>
                            </div>
                        </div>
                        <div class="d-flex mb-3">
                            <div class="text-center">
                                <div class="avatar">
                                    <span
                                        class="avatar-title bg-warning-bright text-warning rounded-circle font-size-22">25</span>
                                </div>
                            </div>
                            <div class="m-l-20">
                                <h5 class="mb-2">
                                    <a class="text-dark">Design Discussion</a>
                                </h5>
                                <p class="mb-0">Let us wax poetic about.</p>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="text-center">
                                <div class="avatar">
                                    <span
                                        class="avatar-title bg-success-bright text-success rounded-circle font-size-22">10</span>
                                </div>
                            </div>
                            <div class="m-l-20">
                                <h5 class="mb-2">
                                    <a class="text-dark">UI Discussion</a>
                                </h5>
                                <p class="mb-0">Let us wax poetic about.</p>
                            </div>
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

    <!-- Circle progress -->
    <script src="{{ url('/vendors/circle-progress/circle-progress.min.js') }}"></script>

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
