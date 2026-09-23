<section class="image-left-right-section py-16 px-4">
    <div class="max-w-7xl mx-auto">
        @php $position = $data['position'] ?? 'left'; @endphp
        <div class="flex flex-col {{ $position === 'right' ? 'md:flex-row-reverse' : 'md:flex-row' }} gap-12 items-center">
            @if(!empty($data['image']))
                <div class="w-full md:w-1/2">
                    <img src="{{ $data['image'] }}" alt="{{ $data['title'] ?? '' }}" class="rounded-lg shadow-lg w-full h-auto" />
                </div>
            @endif
            <div class="w-full {{ !empty($data['image']) ? 'md:w-1/2' : 'w-full' }}">
                @if(!empty($data['title']))
                    <h2 class="text-3xl font-bold mb-4">{{ $data['title'] }}</h2>
                @endif
                @if(!empty($data['description']))
                    <p class="text-gray-600 mb-6 leading-relaxed">{{ $data['description'] }}</p>
                @endif
                @if(!empty($data['button_text']) && !empty($data['button_url']))
                    <a href="{{ $data['button_url'] }}" class="btn btn-primary">{{ $data['button_text'] }}</a>
                @endif
            </div>
        </div>
    </div>
</section>