@php
    $title = $data['title'] ?? 'Bersama Membangun Standar Kesehatan Nasional';
    $subtitle = $data['subtitle'] ?? '';
@endphp

<section class="py-32 relative overflow-hidden bg-white">
    <div class="absolute inset-0 bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-950"></div>
    <div class="absolute top-0 right-0 w-1/3 h-full bg-gradient-to-l from-white/5 to-transparent clip-path-slant"></div>
    <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-primary/40 to-transparent"></div>

    <div class="max-w-7xl mx-auto px-8 relative z-10 text-center">
        <div class="bg-gradient-to-br from-white/10 to-white/5 backdrop-blur-xl rounded-[2rem] p-16 border border-white/20 shadow-2xl">
            <h2 class="font-headline-xl text-headline-xl text-white mb-6 drop-shadow-lg">{!! $title !!}</h2>
            @if($subtitle)
                <p class="text-body-lg text-on-surface-variant max-w-3xl mx-auto mb-12 opacity-90 drop-shadow">{{ $subtitle }}</p>
            @endif
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                <a href="{{ route('page', 'tentang-kami') }}" class="bg-white text-emerald-950 px-10 py-5 rounded-xl font-headline-md text-lg font-semibold shadow-xl hover:scale-105 transition-all">
                    Tentang Kami
                </a>
                <a href="{{ route('contact') }}" class="bg-white/10 backdrop-blur-md border-2 border-white/30 text-white px-10 py-5 rounded-xl font-headline-md text-lg hover:bg-white/20 transition-all">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
</section>