@php
    $heroData = $data['hero'] ?? $data;
    $isList = array_is_list($heroData);
    $slides = $isList ? $heroData : (isset($heroData['title']) || isset($heroData['heading']) ? [$heroData] : []);
    if (empty($slides) && isset($data['title'])) { $slides = [$data]; }
@endphp
@if(empty($slides))
    @php $slides = [['subtitle' => 'Company Profile', 'title' => config('app.name', 'Company'), 'description' => 'Profil perusahaan sederhana — kelola dari CMS.', 'image' => '', 'button1_text' => 'Hubungi Kami', 'button1_link' => route('contact')]]; @endphp
@endif
<div class="hero-main-slider">
    <div class="hs-track">
        @foreach($slides as $index => $slide)
            @php
                $title = $slide['title'] ?? $slide['heading'] ?? '';
                $subtitle = $slide['subtitle'] ?? $slide['eyebrow'] ?? '';
                $desc = $slide['description'] ?? $slide['excerpt'] ?? '';
                $img = $slide['image'] ?? $slide['featured_image'] ?? '';
                $b1t = $slide['button1_text'] ?? $slide['cta_text'] ?? '';
                $b1l = $slide['button1_link'] ?? $slide['cta_link'] ?? '#';
                $b2t = $slide['button2_text'] ?? '';
                $b2l = $slide['button2_link'] ?? '#';
            @endphp
            <div class="hs-slide {{ $index === 0 ? 'active' : '' }}">
                <div class="absolute inset-0 z-0 bg-surface-container-low">
                    @if(!empty($img))
                        <img src="{{ $img }}" alt="{{ $subtitle ?: $title }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-primary/10 via-surface to-surface-container"></div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-r from-white/95 via-white/60 to-transparent z-10"></div>
                </div>
                <div class="relative z-20 max-w-7xl mx-auto px-6 md:px-8 w-full pt-16">
                    <div class="hs-content max-w-2xl bg-white/85 backdrop-blur-md border border-white/60 p-6 md:p-10 rounded-2xl shadow-sm">
                        @if(!empty($subtitle))
                            <span class="text-xs font-semibold tracking-[0.18em] uppercase text-primary mb-3 block">{{ $subtitle }}</span>
                        @endif
                        @if(!empty($title))
                            <h1 class="font-bold text-3xl md:text-4xl lg:text-[44px] leading-tight text-on-surface mb-4">{!! $title !!}</h1>
                        @endif
                        @if(!empty($desc))
                            <p class="text-on-surface-variant leading-relaxed mb-8">{{ $desc }}</p>
                        @endif
                        <div class="flex flex-wrap gap-3">
                            @if(!empty($b1t))
                                <a href="{{ $b1l }}" class="bg-primary text-white px-6 py-3 rounded-full font-medium text-sm inline-flex items-center gap-2 hover:opacity-90 transition-opacity">{{ $b1t }} <span class="material-symbols-outlined text-lg">arrow_forward</span></a>
                            @endif
                            @if(!empty($b2t))
                                <a href="{{ $b2l }}" class="border border-outline-variant bg-white px-6 py-3 rounded-full font-medium text-sm hover:bg-surface-container transition-colors">{{ $b2t }}</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    @if(count($slides) > 1)
        <button class="hs-arrow hs-prev" aria-label="Prev"><span class="material-symbols-outlined">chevron_left</span></button>
        <button class="hs-arrow hs-next" aria-label="Next"><span class="material-symbols-outlined">chevron_right</span></button>
        <div class="hs-dots">
            @foreach($slides as $index => $s)
                <span class="hs-dot {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}"></span>
            @endforeach
        </div>
    @endif
</div>
