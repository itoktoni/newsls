<section class="py-16 px-4">
    <div class="max-w-7xl mx-auto">
        @if(!empty($data['title']))
            <h2 class="text-3xl font-bold mb-4">{{ $data['title'] }}</h2>
        @endif
        @foreach(($fields ?? []) as $field)
            @if(($field['type'] ?? 'text') !== 'container' && !empty($data[$field['name']]))
                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-500 uppercase tracking-wider">{{ $field['label'] ?? $field['name'] }}</label>
                    @if(($field['type'] ?? '') === 'wysiwyg')
                        <div class="prose max-w-none mt-1">{!! $data[$field['name']] !!}</div>
                    @elseif(($field['type'] ?? '') === 'image')
                        <img src="{{ $data[$field['name']] }}" alt="{{ $field['label'] ?? '' }}" class="mt-2 max-w-full h-auto rounded-lg" />
                    @elseif(($field['type'] ?? '') === 'toggle')
                        <span class="mt-1 inline-block">{{ $data[$field['name']] ? 'Yes' : 'No' }}</span>
                    @else
                        <p class="mt-1">{{ $data[$field['name']] }}</p>
                    @endif
                </div>
            @endif
        @endforeach
    </div>
</section>