@extends('cms::frontend.layouts.public')

@section('title', $entry->title)

@section('content')
    <section class="py-24 bg-surface">
        <div class="max-w-4xl mx-auto px-8">
            <h1 class="font-headline-xl text-headline-xl text-on-surface mb-8">{{ $entry->title }}</h1>
            @if(!empty($entry->featured_image))
                <img src="{{ fileUrl($entry->featured_image) }}" alt="{{ $entry->title }}" class="w-full h-auto rounded-2xl mb-8 shadow-lg" />
            @endif
            <div class="text-body-lg text-on-surface-variant leading-relaxed space-y-6">{!! $entry->content !!}</div>
        </div>
    </section>
@endsection