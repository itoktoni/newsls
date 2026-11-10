@props(['items', 'mobile' => false])

@php
    $menu = config('menu.sidebar');
    // Report-only: hanya report, kecuali invoice.
    $reportOnly = auth()->user()?->role === \App\Enums\RoleEnum::USER;
    // Admin: sembunyikan section CMS dan Settings.
    $adminHide = auth()->user()?->role === \App\Enums\RoleEnum::ADMIN;
@endphp

@foreach($menu as $section)
    @if($adminHide && in_array($section['label'], ['CMS', 'Settings'], true)) @continue @endif
    @php
        $sectionItems = $section['items'];
        if ($reportOnly) {
            $sectionItems = array_values(array_filter($sectionItems, fn ($i) => str_starts_with($i['route'], 'report-') && ! str_starts_with($i['route'], 'report-invoice')));
        }
    @endphp
    @if(count($sectionItems) === 0) @continue @endif
    @if($section['label'])
        <div class="px-4 pt-4 pb-1 font-label-caps text-label-caps text-on-surface-variant uppercase tracking-widest">{{ $section['label'] }}</div>
    @endif
    @foreach($sectionItems as $item)
        @php
            // Link Dashboard diarahkan langsung ke dashboard sesuai role
            // (dashboard.admin/rs/laundry), bukan ke route /dashboard yang
            // hanya me-redirect (302) sehingga klik wire:navigate terasa mati.
            $routeName = $item['route'] === 'dashboard' ? dashboardHomeRoute() : $item['route'];
            $url = route($routeName);
            $matchRoutes = $item['match'] ?? [];
            $isActive = request()->routeIs($routeName)
                || request()->routeIs($routeName . '.*')
                || collect($matchRoutes)->contains(fn ($m) => request()->routeIs($m));
        @endphp
        <a
            href="{{ $url }}"
            wire:navigate
            @if($mobile) @click="drawerOpen = false" @endif
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ $mobile ? '' : 'group' }} {{ $isActive ? 'bg-primary text-on-primary font-semibold' : 'bg-surface-container text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface' }}"
        >
            <span class="material-symbols-outlined {{ $isActive ? 'text-on-primary' : 'text-on-surface-variant' . ($mobile ? '' : ' group-hover:text-on-surface') }}">{{ $item['icon'] }}</span>
            <span class="font-body-sm">{{ $item['label'] }}</span>
        </a>
    @endforeach
@endforeach
