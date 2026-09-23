@extends('cms::frontend.layouts.public')

@section('title', $query ? 'Hasil Pencarian: ' . $query : 'Pencarian')

@section('content')
<section class="py-24 bg-surface-container-highest">
    <div class="max-w-7xl mx-auto px-8">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-12">
            <div class="h-px bg-outline-variant flex-grow"></div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface shrink-0 px-4">
                @if($query)
                    Hasil Pencarian: "{{ $query }}"
                @else
                    Pencarian
                @endif
            </h1>
            <div class="h-px bg-outline-variant flex-grow"></div>
        </div>

        {{-- Search Bar --}}
        <div class="max-w-2xl mx-auto mb-12">
            <form action="{{ route('search') }}" method="GET" class="flex gap-3">
                <input type="text" name="q" value="{{ $query }}" placeholder="Cari berita..."
                    class="flex-1 px-6 py-3.5 bg-white border border-outline-variant rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-body-md" />
                <button type="submit" class="bg-primary text-on-primary px-6 py-3.5 rounded-xl font-label-md hover:opacity-90 active:scale-95 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">search</span>
                    Cari
                </button>
            </form>
        </div>

        {{-- Results --}}
        @if($query)
            <p class="text-on-surface-variant text-center mb-8">
                Menampilkan {{ $posts->total() }} hasil untuk "{{ $query }}"
            </p>
        @endif

        @if($posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($posts as $post)
                    <a href="{{ route('blog.post', $post->slug) }}" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all group border border-outline-variant/30">
                        @if(!empty($post->featured_image))
                            <div class="h-52 overflow-hidden">
                                <img src="{{ fileUrl($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" />
                            </div>
                        @else
                            <div class="h-52 bg-surface-container-low flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-outline">article</span>
                            </div>
                        @endif
                        <div class="p-6">
                            <div class="flex items-center gap-3 text-xs text-outline mb-3">
                                @if($post->published_at)
                                    <span>{{ $post->published_at->format('d M Y') }}</span>
                                @endif
                                @if($post->has_type)
                                    <span class="w-1 h-1 bg-outline rounded-full"></span>
                                    <span class="text-primary font-medium">{{ $post->has_type->name }}</span>
                                @endif
                            </div>
                            <h3 class="font-headline-md text-headline-md text-on-surface mb-3 group-hover:text-primary transition-colors line-clamp-2">{{ $post->title }}</h3>
                            @if($post->excerpt)
                                <p class="text-on-surface-variant text-sm line-clamp-3">{{ $post->excerpt }}</p>
                            @endif
                            <div class="mt-4 flex items-center gap-2 text-primary font-label-md text-sm">
                                Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 flex justify-center">
                {{ $posts->links() }}
            </div>
        @elseif($query)
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-6xl text-outline mb-4">search_off</span>
                <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Tidak Ditemukan</h3>
                <p class="text-on-surface-variant">Tidak ada artikel yang cocok dengan "{{ $query }}". Coba kata kunci lain.</p>
            </div>
        @else
            <div class="text-center py-20">
                <span class="material-symbols-outlined text-6xl text-outline mb-4">search</span>
                <h3 class="font-headline-md text-headline-md text-on-surface mb-2">Cari Berita</h3>
                <p class="text-on-surface-variant">Masukkan kata kunci untuk mencari artikel berita.</p>
            </div>
        @endif
    </div>
</section>
@endsection