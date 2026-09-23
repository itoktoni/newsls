@extends('layouts.app')

@section('head')

    <!-- quill -->
    <link href="{{ url('vendors/quill/quill.snow.css') }}" rel="stylesheet" type="text/css">

    <!-- Tagsinput -->
    <link rel="stylesheet" href="{{ url('vendors/tagsinput/bootstrap-tagsinput.css') }}" type="text/css">

@endsection

@section('bodyClass', 'stretch-layout navigation-toggle-one')

@section('content')

    <div class="container-fluid h-100">

        <div class="row app-block">
            <div class="col-md-3 app-sidebar">
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-secondary btn-block" data-toggle="modal" data-target="#compose">
                            Compose Email
                        </button>
                    </div>
                    <div class="app-sidebar-menu">
                        <div class="list-group list-group-flush">
                            <a href="" class="list-group-item active d-flex align-items-center">
                                <i data-feather="mail" class="mr-2 width-15 height-15"></i>
                                Inbox
                                <span class="small ml-auto">14</span>
                            </a>
                            <a href="" class="list-group-item">
                                <i data-feather="send" class="mr-2 width-15 height-15"></i>
                                Send
                            </a>
                            <a href="" class="list-group-item">
                                <i data-feather="edit-3" class="mr-2 width-15 height-15"></i>
                                Draft
                            </a>
                            <a href="" class="list-group-item d-flex align-items-center">
                                <i data-feather="star" class="mr-2 width-15 height-15"></i>
                                Starred
                                <span class="small ml-auto">10</span>
                            </a>
                            <a href="" class="list-group-item">
                                <i data-feather="info" class="mr-2 width-15 height-15"></i>
                                Spam
                            </a>
                            <a href="" class="list-group-item d-flex align-items-center">
                                <i data-feather="trash" class="mr-2 width-15 height-15"></i>
                                Trash
                                <span class="small ml-auto">2</span>
                            </a>
                        </div>
                        <div class="card-body">
                            <h6 class="mb-0">Labels</h6>
                        </div>
                        <div class="list-group list-group-flush">
                            <a href="#" class="list-group-item d-flex align-items-center">
                                <span class="text-warning fa fa-circle mr-2"></span>
                                Theme Support
                                <span class="small ml-auto">5</span>
                            </a>
                            <a href="#" class="list-group-item d-flex align-items-center">
                                <span class="text-success fa fa-circle mr-2"></span>
                                Freelance
                            </a>
                            <a href="#" class="list-group-item d-flex align-items-center">
                                <span class="text-danger fa fa-circle mr-2"></span>
                                Social
                            </a>
                            <a href="#" class="list-group-item d-flex align-items-center">
                                <span class="text-info fa fa-circle mr-2"></span>
                                Friends
                            </a>
                            <a href="#" class="list-group-item d-flex align-items-center">
                                <span class="text-secondary fa fa-circle mr-2"></span>
                                Coding
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 app-content">
                <div class="app-content-overlay"></div>
                <div class="app-action">
                    <div class="action-left">
                        <div class="custom-control custom-checkbox mr-3">
                            <input type="checkbox" class="custom-control-input" id="customCheckAll">
                            <label class="custom-control-label" for="customCheckAll"></label>
                        </div>
                        <ul class="list-inline">
                            <li class="list-inline-item mb-0">
                                <a href="#" class="btn btn-outline-light dropdown-toggle" data-toggle="dropdown">
                                    With selected
                                </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="#">Mark as read</a>
                                    <a class="dropdown-item" href="#">Mark as unread</a>
                                    <a class="dropdown-item" href="#">Spam</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                </div>
                            </li>
                            <li class="list-inline-item mb-0">
                                <a href="#" class="btn btn-outline-light dropdown-toggle" data-toggle="dropdown">
                                    Order by
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#">Date</a>
                                    <a class="dropdown-item" href="#">From</a>
                                    <a class="dropdown-item" href="#">Subject</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#">Size</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="action-right">
                        <form class="d-flex mr-3">
                            <a href="#" class="app-sidebar-menu-button btn btn-outline-light">
                                <i data-feather="menu" class="width-15 height-15"></i>
                            </a>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Email search"
                                       aria-describedby="button-addon1">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-light" type="button" id="button-addon1">
                                        <i class="ti-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                        <div class="app-pager d-flex align-items-center">
                            <div class="mr-3">1-50 of 253</div>
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Previous">
                                            <i data-feather="chevron-left" class="width-15 height-15"></i>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#" aria-label="Next">
                                            <i data-feather="chevron-right" class="width-15 height-15"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="card card-body app-content-body">
                    <div class="app-lists">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck1">
                                        <label class="custom-control-label" for="customCheck1"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star-o font-size-16"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <img src="{{ url('assets/media/image/user/man_avatar5.jpg') }}" class="rounded-circle" alt="image">
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Lucas Kriebel (via Twitter)</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum, labore?</div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-danger"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item active">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" checked id="customCheck2">
                                        <label class="custom-control-label" for="customCheck2"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star font-size-16 text-warning"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <span class="avatar-title bg-info-bright text-info rounded-circle">Z</span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Ecommerce website Paypal integration 😃</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <a href="#" data-toggle="tooltip" title="Attachment">
                                                        <i class="fa fa-paperclip"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-success"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck3">
                                        <label class="custom-control-label" for="customCheck3"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star-o font-size-16"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <span class="avatar-title bg-danger-bright text-danger rounded-circle">S</span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Randy, me (5)</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Corporis cumque dicta eligendi, in quasi recusandae.</div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <a href="#" data-toggle="tooltip" title="Attachment">
                                                        <i class="fa fa-paperclip"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-info"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck4">
                                        <label class="custom-control-label" for="customCheck4"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star-o font-size-16"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <img src="{{ url('assets/media/image/user/women_avatar3.jpg') }}" class="rounded-circle" alt="image">
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Andrew Zimmer ❤❤</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Lorem ipsum dolor sit amet.</div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-secondary"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck5">
                                        <label class="custom-control-label" for="customCheck5"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star font-size-16 text-warning"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <span class="avatar-title bg-info-bright text-info rounded-circle">Z</span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Charukaw, me (7)</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores esse et hic nesciunt quas ratione rem reprehenderit similique temporibus totam!</div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-primary"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck6">
                                        <label class="custom-control-label" for="customCheck6"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star-o font-size-16"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <span class="avatar-title bg-danger-bright text-danger rounded-circle">S</span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Stack Exchange</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Lorem ipsum dolor sit amet.</div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-warning"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck7">
                                        <label class="custom-control-label" for="customCheck7"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star-o font-size-16"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <span class="avatar-title bg-success-bright text-success rounded-circle">K</span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Facebook register completed 👍</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Hey John, Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda, esse.
                                        </div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-success"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck8">
                                        <label class="custom-control-label" for="customCheck8"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star font-size-16 text-warning"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <span class="avatar-title bg-info-bright text-info rounded-circle">Z</span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">Peter, me (3)</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Assumenda cupiditate dolor earum est illo labore nesciunt officia provident quis tempora?</div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <a href="#" data-toggle="tooltip" title="Attachment">
                                                        <i class="fa fa-paperclip"></i>
                                                    </a>
                                                </li>
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-info"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div>
                                    <div class="custom-control custom-checkbox mr-2">
                                        <input type="checkbox" class="custom-control-input" id="customCheck9">
                                        <label class="custom-control-label" for="customCheck9"></label>
                                    </div>
                                </div>
                                <div>
                                    <a href="#" class="add-star mr-3" title="Add stars">
                                        <i class="fa fa-star-o font-size-16"></i>
                                    </a>
                                </div>
                                <div>
                                    <figure class="avatar avatar-sm mr-3">
                                        <span class="avatar-title bg-danger-bright text-danger rounded-circle">S</span>
                                    </figure>
                                </div>
                                <div class="flex-grow-1 min-width-0">
                                    <div class="mb-1 d-flex justify-content-between align-items-center">
                                        <div class="text-truncate app-list-title">How To Set Intentions That Energize You</div>
                                        <div class="pl-3 d-flex">
                                            <span class="text-nowrap text-muted">15 July</span>
                                            <div class="dropdown ml-3">
                                                <a href="#" class="btn btn-outline-light btn-sm" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#">Reply</a>
                                                    <a class="dropdown-item" href="#">Add star</a>
                                                    <a class="dropdown-item" href="#">Add label</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item text-danger" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-muted d-flex justify-content-between">
                                        <div class="text-truncate small">Hey John, bah kivu decrete epanorthotic
                                            unnotched
                                            Argyroneta nonius veratrine preimaginary.
                                        </div>
                                        <div class="text-nowrap pl-3">
                                            <ul class="list-inline">
                                                <li class="list-inline-item mb-0">
                                                    <i class="fa fa-circle text-danger"></i>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <!-- end::app-lists -->

                    <!-- begin:app-detail -->
                    <div class="card app-detail">
                        <div class="card-header">
                            <div class="app-detail-action-left">
                                <a class="app-detail-close-button" href="#">
                                    <i data-feather="arrow-left" class="mr-3"></i>
                                </a>
                                <h5 class="mb-0">
                                    Advertising Internet Online
                                    <span class="badge bg-success badge-pill ml-1">Freelance</span>
                                </h5>
                            </div>
                            <div class="app-detail-action-right">
                                <div>
                                    <a href="#" class="btn btn-outline-light" data-toggle="tooltip" title="Reply">
                                        <i data-feather="corner-up-left"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-light ml-2" data-toggle="tooltip"
                                       title="Mark as spam">
                                        <i data-feather="info"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-light ml-2" data-toggle="tooltip"
                                       title="Dispose">
                                        <i data-feather="trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="app-detail-article">
                            <div class="card-body">
                                <div class="d-flex align-items-center p-l-r-0 m-b-20">
                                    <div class="d-flex align-items-center">
                                        <figure class="avatar avatar-sm m-r-15">
                                            <img src="{{ url('assets/media/image/user/women_avatar5.jpg') }}" class="rounded-circle"
                                                 alt="image">
                                        </figure>
                                        <div>
                                            <h6 class="m-b-0">
                                                <span class="text-primary">Valentine Maton</span>
                                                <span class="d-sm-inline d-none text-muted"> &lt;elnora@gmail.com&gt;</span>
                                            </h6>
                                            <span class="small text-muted">to Lois Jimenez</span>
                                        </div>
                                    </div>
                                    <div class="ml-auto">
                                        <a href="#" data-toggle="tooltip" title="Attachment" class="mr-2">
                                            <i class="fa fa-paperclip"></i>
                                        </a>
                                        <a href="#" class="mr-2">
                                            <i class="fa fa-star font-size-16 text-warning"></i>
                                        </a>
                                        <span class="text-muted">4:14 AM</span>
                                    </div>
                                </div>
                                <p>Greetings!</p>
                                <p>It is a long established fact that a reader will be distracted by the readable
                                    content of
                                    a page when looking at its layout.The point of using Lorem Ipsum is that it has a
                                    more-or-less normal distribution of letters, as opposed to using 'Content here,
                                    content
                                    here',making it look like readable English.</p>
                                <p>There are many variations of passages of Lorem Ipsum available, but the majority have
                                    suffered alteration in some form, by injected humour, or randomised words which
                                    don't
                                    look even slightly believable.</p>
                                <p>Sincerely yours,</p>
                                <p>
                                    <strong>Envato Design Team</strong>
                                </p>
                            </div>
                            <hr class="m-0">
                            <div class="card-body">
                                <h6 class="mb-3 font-size-11 text-uppercase">Attached Files</h6>
                                <ul class="list-unstyled mb-0">
                                    <li class="small">
                                        <a href="#">
                                            <i data-feather="paperclip" class="mr-1 width-15 height-15"></i>
                                            uikit-design.psd
                                        </a>
                                    </li>
                                    <li class="small">
                                        <a href="#">
                                            <i data-feather="paperclip" class="mr-1 width-15 height-15"></i>
                                            uikit-design.sketch
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <hr class="m-0">
                            <div class="card-body">
                                <h6 class="mb-3 font-size-11 text-uppercase">Reply</h6>
                                <div class="reply-email-quill-editor mb-3"></div>
                                <div class="d-flex justify-content-between">
                                    <div class="reply-email-quill-toolbar">
                                        <span class="ql-formats mr-0">
                                          <button class="ql-bold"></button>
                                          <button class="ql-italic"></button>
                                          <button class="ql-underline"></button>
                                          <button class="ql-link"></button>
                                          <button class="ql-image"></button>
                                        </span>
                                    </div>
                                    <button class="btn btn-primary">
                                        <i data-feather="send" class="mr-2"></i>
                                        Send
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="compose">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Compose Email</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i class="ti-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between m-b-20">
                        <div>
                            <a href="#" data-toggle="tooltip" title=""
                               class="btn btn-outline-light btn-sm mr-2"
                               data-original-title="Keep">
                                <i data-feather="save"></i>
                            </a>
                            <a href="#" data-toggle="tooltip" title=""
                               class="btn btn-outline-light btn-sm mr-2"
                               data-original-title="Delete">
                                <i data-feather="trash-2"></i>
                            </a>
                        </div>
                    </div>
                    <form>
                        <div class="form-group">
                            <input type="text" class="form-control tagsinput" placeholder="To"
                                   value="example@test.com.tr" required>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Subject" required>
                        </div>
                        <div class="form-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" multiple id="customFileLangHTML">
                                <label class="custom-file-label" for="customFileLangHTML" data-browse="Select files">Voeg je document toe</label>
                            </div>
                        </div>
                        <div>
                            <div class="compose-quill-editor mb-3"></div>
                            <div class="d-flex justify-content-between">
                                <div class="compose-quill-toolbar">
                            <span class="ql-formats mr-0">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-link"></button>
                                <button class="ql-image"></button>
                            </span>
                                </div>
                                <button class="btn btn-primary">
                                    <i data-feather="send" class="mr-2"></i>
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')

    <!-- Tagsinput -->
    <script src="{{ url('vendors/tagsinput/bootstrap-tagsinput.js') }}"></script>
    <script src="{{ url('assets/js/examples/tagsinput.js') }}"></script>

    <!-- quill -->
    <script src="{{ url('vendors/quill/quill.js') }}"></script>

    <script>
        new Quill('.compose-quill-editor', {
            modules: {
                toolbar: ".compose-quill-toolbar"
            },
            placeholder: "Type something... ",
            theme: "snow"
        });

        new Quill('.reply-email-quill-editor', {
            modules: {
                toolbar: ".reply-email-quill-toolbar"
            },
            placeholder: "Type something... ",
            theme: "snow"
        });

        $(function () {
            $(document).on('click', '.app-block .app-content .app-action .action-left input[type="checkbox"]', function () {
                $('.app-lists ul li input[type="checkbox"]').prop('checked', $(this).prop('checked'));
                if ($(this).prop('checked')) {
                    $('.app-lists ul li input[type="checkbox"]').closest('li').addClass('active');
                } else {
                    $('.app-lists ul li input[type="checkbox"]').closest('li').removeClass('active');
                }
            });

            $(document).on('click', '.app-lists ul li input[type="checkbox"]', function () {
                if ($(this).prop('checked')) {
                    $(this).closest('li').addClass('active');
                } else {
                    $(this).closest('li').removeClass('active');
                }
            });

            $(document).on('click', '.app-block .app-content .app-content-body .app-lists ul.list-group li.list-group-item', function (e) {
                if (!$(e.target).is('.custom-control, .custom-control *, a, a *')) {
                    $('.app-detail').addClass('show').one('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function () {
                        $('.app-block .app-content .app-content-body .app-detail .app-detail-article').niceScroll().resize();
                    });
                }
            });

            $(document).on('click', 'a.app-detail-close-button', function () {
                $('.app-detail').removeClass('show');
                return false;
            });

            $(document).on('click', '.app-sidebar-menu-button', function () {
                $('.app-block .app-sidebar, .app-content-overlay').addClass('show');
                // $('.app-block .app-sidebar .app-sidebar-menu').niceScroll().resize();
                return false;
            });

            $(document).on('click', '.app-content-overlay', function () {
                $('.app-block .app-sidebar, .app-content-overlay').removeClass('show');
                return false;
            });
        });
    </script>

@endsection
