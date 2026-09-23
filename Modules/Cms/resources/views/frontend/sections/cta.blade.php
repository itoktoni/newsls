@php
    $ctaData = $data['cta'] ?? $data;
    $title = $ctaData['title'] ?? $ctaData['heading'] ?? 'Siap bekerja sama?';
    $description = $ctaData['description'] ?? 'Hubungi kami untuk diskusi kebutuhan Anda.';
    $button1Text = $ctaData['button1_text'] ?? $ctaData['cta_text'] ?? 'Hubungi Kami';
    $button1Link = $ctaData['button1_link'] ?? $ctaData['cta_link'] ?? route('contact');
    $button2Text = $ctaData['button2_text'] ?? '';
    $button2Link = $ctaData['button2_link'] ?? '#';
    $image = $ctaData['image'] ?? '';
@endphp
<section class="py-16 bg-surface-container-low border-y border-outline-variant/10">
    <div class="max-w-7xl mx-auto px-6 md:px-8 flex flex-col md:flex-row items-center gap-8">
        <div class="flex-1">
            <h2 class="text-2xl md:text-3xl font-bold text-on-surface">{{ $title }}</h2>
            <p class="text-on-surface-variant mt-3 max-w-xl">{{ $description }}</p>
            <div class="flex flex-wrap gap-3 mt-6">
                <a href="{{ $button1Link }}" class="bg-primary text-white px-7 py-3 rounded-full font-medium text-sm hover:opacity-90 transition-opacity">{{ $button1Text }}</a>
                @if($button2Text)
                    <a href="{{ $button2Link }}" class="bg-white border border-outline-variant text-on-surface px-7 py-3 rounded-full font-medium text-sm hover:bg-white transition-colors">{{ $button2Text }}</a>
                @endif
            </div>
        </div>
        @if($image)
            <div class="w-full md:w-72 shrink-0 rounded-2xl overflow-hidden border border-outline-variant/20 bg-white p-1">
                <img src="{{ $image }}" alt="" class="rounded-xl w-full object-cover" />
            </div>
        @endif
    </div>
</section>
