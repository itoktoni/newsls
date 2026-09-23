<nav class="fixed top-0 w-full z-50 bg-white/80 backdrop-blur-md border-b border-outline-variant/20" id="main-nav">
    <div class="flex justify-between items-center px-6 md:px-8 py-4 max-w-7xl mx-auto">
        <a href="{{ url('/') }}" class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-lg bg-primary text-on-primary flex items-center justify-center font-headline-md text-sm font-bold">{{ Str::upper(Str::substr(config('app.name', 'CP'), 0, 2)) }}</span>
            <span class="font-headline-md text-headline-md font-bold text-on-surface hidden sm:inline">{{ config('app.name', 'Company') }}</span>
        </a>
        <div class="hidden md:flex items-center gap-8">
            @if($menu && $menu->items)
                @php $navItems = collect($menu->items)->sortBy('sort_order')->values(); @endphp
                @foreach($navItems as $item)
                    @if(!empty($item['children']) && count($item['children']) > 0)
                        <div class="relative group">
                            <button class="font-label-md text-sm {{ request()->is(trim($item['url'],'/').'*') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} flex items-center gap-1 transition-colors">
                                {{ $item['label'] }}
                                <span class="material-symbols-outlined text-sm group-hover:rotate-180 transition-transform">expand_more</span>
                            </button>
                            <div class="absolute left-0 top-full pt-2 hidden group-hover:block z-50">
                                <div class="bg-white rounded-xl shadow-lg border border-outline-variant/20 py-2 min-w-[220px]">
                                    @foreach($item['children'] as $child)
                                        <a href="{{ $child['url'] ?? '#' }}" target="{{ $child['target'] ?? '_self' }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors">
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ $item['url'] }}" class="font-label-md text-sm {{ request()->is(trim($item['url'],'/')) || (trim($item['url'],'/')=='' && request()->is('/')) ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }} transition-colors">{{ $item['label'] }}</a>
                    @endif
                @endforeach
            @else
                <a href="{{ url('/') }}" class="font-label-md text-sm {{ request()->is('/') ? 'text-primary' : 'text-on-surface-variant hover:text-primary' }}">Beranda</a>
                <a href="{{ url('/#produk') }}" class="font-label-md text-sm text-on-surface-variant hover:text-primary">Produk</a>
                <a href="{{ url('/#tentang') }}" class="font-label-md text-sm text-on-surface-variant hover:text-primary">Tentang</a>
                <a href="{{ route('contact') }}" class="font-label-md text-sm text-on-surface-variant hover:text-primary">Kontak</a>
            @endif
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('search') }}" class="w-9 h-9 rounded-full border border-outline-variant/30 flex items-center justify-center text-on-surface-variant hover:text-primary hover:border-primary/30 transition-colors" aria-label="Search"><span class="material-symbols-outlined text-xl">search</span></a>
            <a href="{{ route('contact') }}" class="hidden sm:inline-flex bg-primary text-on-primary px-5 py-2.5 rounded-full font-label-md text-sm hover:opacity-90 active:scale-95 transition-all">Pesan Sekarang</a>
        </div>
    </div>
</nav>
