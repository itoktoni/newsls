@extends('cms::frontend.layouts.public')

@section('title', $homeEntry?->title ?? config('app.name', 'LARAVEL'))

@section('content')

@if ($homeHtml)
    {!! $homeHtml !!}
@else
    <section class="py-32 bg-surface-container-low min-h-screen flex items-center">
        <div class="max-w-4xl mx-auto px-8 text-center">
            <span class="material-symbols-outlined text-6xl text-primary/40 mb-4 block">home</span>
            <h1 class="font-headline-xl text-headline-xl text-on-surface mb-4">{{ config('app.name', 'LARAVEL') }}</h1>
            <p class="text-on-surface-variant text-body-lg mb-8">Selamat datang di situs resmi. Belum ada konten homepage yang dipublikasikan — buat konten dengan tipe <strong>Homepage</strong> lalu set status <strong>Published</strong>.</p>
            <a href="{{ route('login') }}" class="bg-primary text-on-primary px-8 py-4 rounded-xl font-label-md hover:opacity-90 transition-all">Login Admin</a>
        </div>
    </section>
@endif

@endsection