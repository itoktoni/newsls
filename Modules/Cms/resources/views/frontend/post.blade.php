@extends('cms::frontend.layouts.public')

@section('title', $entry->title)

@section('content')
    <article class="max-w-4xl mx-auto px-8 py-24">
        {{-- Post Header --}}
        <header class="mb-8">
            @php
                $categoryNames = $entry->has_categories()->get();
                $tagNames = $entry->has_tags()->get();
            @endphp

            @if($categoryNames->count() > 0)
                <div class="flex gap-2 mb-4">
                    @foreach($categoryNames as $category)
                        <a href="{{ route('blog.category', $category->slug) }}" class="text-xs font-medium text-primary bg-primary/10 px-3 py-1.5 rounded-full hover:bg-primary/20 transition-colors">{{ $category->name }}</a>
                    @endforeach
                </div>
            @endif

            <h1 class="font-headline-xl text-headline-xl text-on-surface mb-4">{{ $entry->title }}</h1>

            <div class="flex items-center gap-4 text-outline text-sm">
                @if($entry->published_at)
                    <span>{{ $entry->published_at->format('d M Y') }}</span>
                @endif
            </div>
        </header>

        {{-- Featured Image --}}
        @if(!empty($entry->featured_image))
            <figure class="mb-8">
                <img src="{{ fileUrl($entry->featured_image) }}" alt="{{ $entry->title }}" class="w-full h-auto rounded-2xl shadow-lg" />
            </figure>
        @endif

        {{-- Excerpt --}}
        @if(!empty($entry->excerpt))
            <div class="text-body-lg text-on-surface-variant italic mb-8 border-l-4 border-secondary-container pl-4">
                {{ $entry->excerpt }}
            </div>
        @endif

        {{-- Content --}}
        <div class="text-body-lg text-on-surface-variant leading-relaxed space-y-6 mb-12">
            {!! $entry->content !!}
        </div>

        {{-- Tags --}}
        @if($tagNames->count() > 0)
            <div class="flex gap-2 flex-wrap mb-8 pt-8 border-t border-outline-variant/30">
                @foreach($tagNames as $tag)
                    <a href="{{ route('blog.tag', $tag->slug) }}" class="text-xs text-outline bg-surface-container-low px-3 py-1.5 rounded-full hover:bg-surface-container transition-colors">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        {{-- Back to Blog --}}
        <div class="pt-8 border-t border-outline-variant/30">
            <a href="{{ route('blog') }}" class="text-primary font-label-md inline-flex items-center gap-2 hover:gap-3 transition-all">
                <span class="material-symbols-outlined">arrow_back</span>
                Kembali ke Berita
            </a>
        </div>
    </article>
@endsection