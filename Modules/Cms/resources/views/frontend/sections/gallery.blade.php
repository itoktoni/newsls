<section class="gallery-section py-16 px-4">
    <div class="max-w-7xl mx-auto">
        @php
            $columns = $data['columns'] ?? '3';
            $colClass = match($columns) {
                '2' => 'grid-cols-2',
                '3' => 'grid-cols-3',
                '4' => 'grid-cols-2 md:grid-cols-4',
                default => 'grid-cols-3',
            };
            $images = $data['images'] ?? [];
            if (is_string($images)) {
                $images = json_decode($images, true) ?? [$images];
            }
        @endphp
        <div class="grid {{ $colClass }} gap-4">
            @foreach($images as $image)
                <div class="overflow-hidden rounded-lg">
                    <img src="{{ $image }}" alt="" class="w-full h-64 object-cover hover:scale-105 transition-transform duration-300" />
                    @if($data['caption'] ?? false)
                        <p class="text-sm text-gray-500 mt-2 text-center">{{ basename($image) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>