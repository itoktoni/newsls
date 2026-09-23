@php
    $cards = $data['services'] ?? (array_is_list($data) ? $data : []);
    $heading = $data['heading'] ?? 'Produk Segar';
    $subheading = $data['subheading'] ?? 'Sayur-mayur, telur, ikan, ayam, daging & bahan dapur — segar setiap hari.';
@endphp
<section id="produk" class="py-16 md:py-20 bg-surface-container-low">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-10">
            <div class="max-w-2xl">
                <div class="w-10 h-1 bg-primary rounded-full mb-4"></div>
                <h2 class="text-2xl md:text-3xl font-bold text-on-surface">{{ $heading }}</h2>
                <p class="text-on-surface-variant mt-2">{{ $subheading }}</p>
            </div>
            <a href="{{ route('contact') }}" class="text-primary font-medium inline-flex items-center gap-1.5 hover:gap-2 transition-all shrink-0">Pesan Sekarang <span class="material-symbols-outlined text-xl">arrow_right_alt</span></a>
        </div>
        @if(empty($cards))
            <p class="text-on-surface-variant text-sm">Belum ada produk — tambah dari CMS (Content → homepage → Produk).</p>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($cards as $card)
                    @php
                        $title = $card['title'] ?? $card['name'] ?? '';
                        $desc = $card['description'] ?? $card['excerpt'] ?? '';
                        $icon = $card['icon'] ?? 'work';
                        $features = $card['features'] ?? [];
                    @endphp
                    <div class="bg-white rounded-2xl p-7 border border-outline-variant/20 hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-xl bg-primary/10 text-primary flex items-center justify-center mb-5">
                            <span class="material-symbols-outlined">{{ $icon }}</span>
                        </div>
                        <h3 class="font-semibold text-on-surface mb-2">{{ $title }}</h3>
                        <p class="text-sm text-on-surface-variant leading-relaxed">{{ $desc }}</p>
                        @if(!empty($features))
                            <ul class="mt-4 space-y-2">
                                @foreach($features as $f)
                                    <li class="flex gap-2 text-sm text-on-surface-variant"><span class="w-1.5 h-1.5 rounded-full bg-primary mt-2 shrink-0"></span><span>{{ $f['text'] ?? $f }}</span></li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
