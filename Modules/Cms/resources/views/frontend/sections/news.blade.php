@php
    $newsData = $data['news'] ?? $data;
    $mainArticle = $newsData['main_article'] ?? null;
    $sideArticles = $newsData['side_articles'] ?? [];
@endphp

<section class="py-24 bg-surface-container-highest">
    <div class="max-w-7xl mx-auto px-8">
        <div class="flex items-center gap-4 mb-12">
            <div class="h-px bg-outline-variant flex-grow"></div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface shrink-0 px-4">Berita &amp; Informasi</h2>
            <div class="h-px bg-outline-variant flex-grow"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 h-auto md:h-[600px]">
            {{-- Main Feature --}}
            @if($mainArticle)
                <div class="md:col-span-7 h-full relative group rounded-2xl overflow-hidden shadow-xl">
                    @if(!empty($mainArticle['image']))
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            data-alt="{{ $mainArticle['title'] ?? '' }}"
                            src="{{ $mainArticle['image'] }}" />
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-inverse-surface/90 via-inverse-surface/40 to-transparent p-12 flex flex-col justify-end">
                        <span class="font-label-md text-secondary-fixed-dim mb-4">{{ $mainArticle['category'] ?? 'ARTIKEL' }} • {{ $mainArticle['year'] ?? '' }}</span>
                        <h3 class="font-headline-xl text-headline-xl text-white mb-6">{{ $mainArticle['title'] ?? '' }}</h3>
                        <button class="text-white flex items-center gap-2 font-label-md hover:translate-x-2 transition-transform w-fit">
                            {{ $mainArticle['link_text'] ?? 'Baca Selengkapnya' }} <span class="material-symbols-outlined">north_east</span>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Side Grid --}}
            <div class="md:col-span-5 grid grid-rows-{{ count($sideArticles) }} gap-6 h-full">
                @foreach($sideArticles as $article)
                    <div class="p-10 rounded-2xl flex flex-col justify-between group cursor-pointer hover:shadow-2xl transition-all"
                        style="background-color: {{ $article['bg_color'] ?? '#ffffff' }}; {{ ($article['bg_color'] ?? '#ffffff') === '#ffffff' ? 'border: 1px solid #becabf33;' : '' }}">
                        <div>
                            <span class="material-symbols-outlined text-4xl mb-4 {{ ($article['bg_color'] ?? '#ffffff') === '#ffffff' ? 'text-primary' : 'text-secondary-container' }}">{{ $article['icon'] ?? '' }}</span>
                            <h4 class="font-headline-md text-headline-md mb-4 {{ ($article['bg_color'] ?? '#ffffff') === '#ffffff' ? 'text-on-surface' : 'text-on-primary' }}">{{ $article['title'] ?? '' }}</h4>
                            <p class="{{ ($article['bg_color'] ?? '#ffffff') === '#ffffff' ? 'text-on-surface-variant' : 'opacity-80' }} line-clamp-2">{{ $article['description'] ?? '' }}</p>
                        </div>
                        <span class="font-label-md mt-6 group-hover:translate-x-2 transition-transform flex items-center gap-2 {{ ($article['bg_color'] ?? '#ffffff') === '#ffffff' ? 'text-primary' : '' }}">
                            {{ $article['link_text'] ?? '' }} <span class="material-symbols-outlined">arrow_right_alt</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>