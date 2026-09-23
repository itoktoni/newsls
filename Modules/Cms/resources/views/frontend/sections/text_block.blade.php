<section class="text-block-section py-16 px-4">
    <div class="max-w-4xl mx-auto">
        @if(!empty($data['title']))
            <h2 class="text-3xl font-bold mb-6">{{ $data['title'] }}</h2>
        @endif
        @if(!empty($data['content']))
            <div class="prose prose-lg max-w-none">{!! $data['content'] !!}</div>
        @endif
    </div>
</section>