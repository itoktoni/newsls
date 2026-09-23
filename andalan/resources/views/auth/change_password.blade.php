@extends('layouts.auth')

@section('content')

    <h5>Form Ganti Password</h5>

    <form method="POST" action="/change-password">
        @csrf
        <div class="form-group d-flex align-items-center">
            <input type="password" name="password" class="form-control" placeholder="New Password" required autofocus>
        </div>
        <button class="btn btn-primary btn-block">Ganti Password</button>
        <hr>
        <a href="{{ route('home') }}" class="btn btn-sm btn-outline-light ml-1">
            Kembali ke Beranda
        </a>
    </form>

</div>

@endsection
