@php
    $title = $data['title'] ?? $data['heading'] ?? 'Tentang Kami';
    $subtitle = $data['subtitle'] ?? '';
    $description = $data['description'] ?? $data['content'] ?? '';
    $image = $data['image'] ?? $data['featured_image'] ?? '';
    $stats = $data['stats'] ?? [];
    $ctaText = $data['cta_text'] ?? '';
    $ctaLink = $data['cta_link'] ?? route('contact');
@endphp
<section id="tentang" class="py-16 md:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-6 md:px-8">
        <div class="grid md:grid-cols-2 gap-10 items-center">
            <div>
                @if($subtitle)<p class="text-xs font-semibold tracking-[0.16em] uppercase text-primary mb-2">{{ $subtitle }}</p>@endif
                <h2 class="text-2xl md:text-3xl font-bold text-on-surface leading-tight">{{ $title }}</h2>
                @if($description)<div class="prose prose-sm max-w-none mt-4 text-on-surface-variant leading-relaxed">{!! is_string($description) ? nl2br(e($description)) : '' !!}</div>@endif
                @if(!empty($stats))
                    <div class="grid grid-cols-3 gap-4 mt-8">
                        @foreach($stats as $stat)
                            <div class="rounded-xl border border-outline-variant/20 p-4 text-center bg-surface-container-low/50">
                                <div class="text-xl font-bold text-primary">{{ $stat['value'] ?? '' }}</div>
                                <div class="text-xs text-on-surface-variant mt-1">{{ $stat['label'] ?? '' }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif
                @if($ctaText)
                    <a href="{{ $ctaLink }}" class="inline-flex mt-8 bg-primary text-white px-6 py-3 rounded-full text-sm font-medium hover:opacity-90 transition-opacity">{{ $ctaText }}</a>
                @endif
            </div>
            <div class="rounded-2xl overflow-hidden border border-outline-variant/20 bg-surface-container-low aspect-[4/3] flex items-center justify-center">
                @if($image)
                    <img src="{{ $image }}" alt="{{ $title }}" class="w-full h-full object-cover" />
                @else
                    <span class="material-symbols-outlined text-5xl text-outline-variant">apartment</span>
                @endif
            </div>
        </div>
    </div>
</section>
