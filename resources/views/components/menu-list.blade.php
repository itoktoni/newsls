@props(['items' => [], 'title' => 'Menu'])

<div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4 form-card">
    <h3 class="font-headline-md text-headline-md text-on-surface mb-4">{{ $title }}</h3>
    <div class="divide-y divide-outline-variant/50">
        @foreach($items as $item)
        <a href="{{ $item['url'] ?? '#' }}" class="flex items-center gap-3 py-3 px-2 hover:bg-surface-container rounded-lg transition-colors">
            <span class="material-symbols-outlined text-on-surface-variant">{{ $item['icon'] ?? 'arrow_right' }}</span>
            <span class="flex-1 font-body-sm text-body-sm text-on-surface">{{ $item['label'] ?? '' }}</span>
            <span class="material-symbols-outlined text-sm text-on-surface-variant">chevron_right</span>
        </a>
        @endforeach
    </div>
</div>
