@php
    $slides = $data['slider'] ?? $data['carousel'] ?? [];
    // Fallback: when passed directly as slider array (e.g. via ContainerRenderer auto-map)
    if (empty($slides) && isset($data[0]) && is_array($data[0])) {
        $slides = $data;
    }
    $slides = is_array($slides) ? array_values(array_filter($slides, fn ($s) => is_array($s))) : [];
@endphp

@if(!empty($slides))
<section class="slider-section py-16 px-4" data-autoplay="true" data-dots="true">
    <div class="max-w-7xl mx-auto">
        <div class="swiper">
            <div class="swiper-wrapper">
                @foreach($slides as $slide)
                    @php
                        $img = $slide['slider_image'] ?? $slide['image'] ?? '';
                        $title = $slide['slider_text'] ?? $slide['text'] ?? $slide['title'] ?? '';
                        $desc = $slide['slider_description'] ?? $slide['description'] ?? $slide['subtitle'] ?? '';
                        $btn = $slide['button'] ?? $slide['button1_text'] ?? '';
                    @endphp
                    <div class="swiper-slide">
                        <div class="relative">
                            @if(!empty($img))
                                <img src="{{ $img }}" alt="{{ $title }}" class="w-full h-full object-cover rounded-lg" />
                            @endif
                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-lg">
                                <div class="text-center text-white px-4">
                                    @if(!empty($title))
                                        <p class="text-lg font-semibold mb-2">{{ $title }}</p>
                                    @endif
                                    @if(!empty($desc))
                                        <p class="text-sm text-white/80 mb-4">{{ $desc }}</p>
                                    @endif
                                    @if(!empty($btn))
                                        <a href="#" class="btn btn-primary">{{ $btn }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
@endif