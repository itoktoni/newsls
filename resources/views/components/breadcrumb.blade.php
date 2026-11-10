@props(['items' => []])
<nav class="font-body-sm text-on-surface-variant mb-4 flex items-center gap-2" aria-label="Breadcrumb">
    @foreach($items as $item)
        @if(!$loop->last)
            @php
                // "Home" breadcrumb menunjuk ke route /dashboard yang hanya me-redirect (302);
                // arahkan langsung ke dashboard sesuai role agar klik wire:navigate berfungsi.
                $crumbUrl = in_array($item['url'], ['/dashboard', route('dashboard')], true)
                    ? dashboardHomeUrl()
                    : $item['url'];
            @endphp
            <a href="{{ $crumbUrl }}" wire:navigate class="cursor-pointer hover:text-primary transition-colors">{{ $item['label'] }}</a>
            <span class="material-symbols-outlined text-sm">chevron_right</span>
        @else
            <span class="font-medium text-primary">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
