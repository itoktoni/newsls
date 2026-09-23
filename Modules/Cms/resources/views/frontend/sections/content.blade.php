<section class="py-16 px-4">
    <div class="max-w-7xl mx-auto">
        @if(!empty($data['image']))
            <div class="mb-8">
                <img src="{{ $data['image'] }}" alt="{{ $data['title'] ?? '' }}" class="w-full h-64 object-cover rounded-lg" />
            </div>
        @endif

        <div class="container mx-auto">
            @if(!empty($data['title']))
                <h2 class="font-headline-lg text-headline-lg text-on-surface mb-4">{{ $data['title'] }}</h2>
            @endif
            @if(!empty($data['content']))
                <div class="text-on-surface-variant text-body-lg">
                    {!! $data['content'] !!}
                </div>
            @endif
        </div>
    </div>
</section>