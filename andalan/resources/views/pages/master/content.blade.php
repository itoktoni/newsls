@extends('layouts.app')

@section('content')

<div class="container-fluid">
	@yield('container')
</div>

@endsection

@push('footer')
@stack('javascript')
@endpush