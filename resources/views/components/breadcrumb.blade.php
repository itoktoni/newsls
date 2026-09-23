@props(['items' => []])
<nav class="font-body-sm text-on-surface-variant mb-4 flex items-center gap-2" aria-label="Breadcrumb">
    @foreach($items as $item)
        @if(!$loop->last)
            <a href="{{ $item['url'] }}" wire:navigate class="cursor-pointer hover:text-primary transition-colors">{{ $item['label'] }}</a>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
        @else
            <span class="font-medium text-primary">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
