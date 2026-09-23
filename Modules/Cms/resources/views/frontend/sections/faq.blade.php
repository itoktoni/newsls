<section class="faq-section py-16 px-4">
    <div class="max-w-4xl mx-auto">
        @if(!empty($data['title']))
            <h2 class="text-3xl font-bold mb-8 text-center">{{ $data['title'] }}</h2>
        @endif
        <div class="space-y-4">
            @if(!empty($data['items']))
                @foreach($data['items'] as $index => $item)
                    <div class="collapse collapse-arrow bg-base-200 rounded-lg">
                        <input type="radio" name="faq-{{ $index }}" />
                        <div class="collapse-title text-lg font-medium">
                            {{ $item['question'] ?? '' }}
                        </div>
                        <div class="collapse-content">
                            <p class="text-gray-600">{{ $item['answer'] ?? '' }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>