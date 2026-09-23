@php
    $cards = $data['certifications'] ?? [];
@endphp

<section class="py-24 bg-surface-container-low">
    <div class="max-w-7xl mx-auto px-8">
        <div class="text-center mb-16">
            <h2 class="font-headline-lg text-headline-lg text-on-surface">Standar Kualitas Kami</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($cards as $card)
                <div class="glass-card p-8 rounded-xl flex flex-col items-center text-center group hover:-translate-y-2 transition-all duration-300 relative overflow-hidden">
                    <div class="absolute inset-0 gold-shimmer opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="w-24 h-24 mb-6 relative">
                        @if(!empty($card['image']))
                            <img class="w-full h-full object-contain" data-alt="{{ $card['text'] ?? '' }}" src="{{ $card['image'] }}" />
                        @endif
                    </div>
                    <h3 class="font-headline-md text-headline-md mb-2">{{ $card['text'] ?? '' }}</h3>
                    <p class="text-on-surface-variant font-body-md">{{ $card['description'] ?? '' }}</p>
                    @if(!empty($card['link_text']))
                        <div class="mt-6 text-secondary-container flex items-center gap-2 font-label-md">
                            {{ $card['link_text'] }} <span class="material-symbols-outlined text-sm">open_in_new</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>