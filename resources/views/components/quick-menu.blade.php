@props(['items' => [], 'title' => 'Quick Menu'])

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 form-card">
    <h3 class="font-headline-md text-headline-md text-on-surface mb-4">{{ $title }}</h3>
    <div class="grid grid-cols-4 lg:grid-cols-8 gap-3">
        @foreach($items as $item)
        <a href="{{ $item['url'] ?? '#' }}" class="group flex flex-col items-center gap-2 p-3 rounded-2xl bg-surface-container hover:bg-surface-container-high border border-outline-variant transition-all">
            <span class="material-symbols-outlined text-2xl {{ $item['icon_color'] ?? 'text-primary' }} group-hover:scale-110 transition-transform">{{ $item['icon'] ?? 'arrow_right' }}</span>
            <span class="font-label-caps text-label-caps text-on-surface text-center">{{ $item['label'] ?? '' }}</span>
        </a>
        @endforeach
    </div>
</div>
