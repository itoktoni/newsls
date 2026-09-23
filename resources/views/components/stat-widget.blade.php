@props(['items' => []])

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    @foreach($items as $item)
    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 rounded-xl {{ $item['bg_color'] ?? 'bg-primary/10' }} flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl {{ $item['icon_color'] ?? 'text-primary' }}">{{ $item['icon_name'] ?? 'analytics' }}</span>
            </div>
            @if(isset($item['badge']))
            <span class="font-label-caps text-label-caps {{ $item['badge_class'] ?? 'bg-primary-fixed text-primary' }} px-2 py-1 rounded-full">{{ $item['badge'] }}</span>
            @endif
        </div>
        <p class="font-headline-lg text-headline-lg text-on-surface">{{ $item['value'] ?? '' }}</p>
        <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">{{ $item['label'] ?? '' }}</p>
    </div>
    @endforeach
</div>
