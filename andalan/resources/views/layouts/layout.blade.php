@extends(Template::master())

@section('title')
    {{ $label }}
@endsection

@section('container')
    <div class="container-fluid">
        <div id="errormessages"></div>
        <div class="row">
            <div class="col-12">
                {{ $slot }}
            </div>
        </div>
    </div>
@endsection
