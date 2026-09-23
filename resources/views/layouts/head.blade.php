<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="notification-enabled" content="{{ config('langkahkecil.notification_enable') ? 'true' : 'false' }}"/>
    @if(config('centrifugo.url'))
    <meta name="centrifugo-url" content="{{ str_replace(['http://', 'https://'], ['ws://', 'wss://'], config('centrifugo.url')) }}/connection/websocket"/>
    @endif
    @auth
    <meta name="user-id" content="{{ auth()->id() }}"/>
    @endauth
    @php
        $faviconUrl = \App\Models\WebsiteSetting::fileUrl(\App\Models\WebsiteSetting::merged()['favicon'] ?? null) ?? '/favicon.ico';
    @endphp
    <title>{{ config('website.name', $title ?? 'CMS') }}</title>
    <link rel="icon" href="{{ $faviconUrl }}" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/notifications.js'])
    @livewireStyles
    {{-- Self-hosted apexcharts: CDN sync script di head memperlambat first paint saat reload --}}
    <script src="{{ asset('js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('js/table.js') }}"></script>
    {{-- Dynamic theme color overrides from website settings --}}
    <style>
        :root {
            --color-primary: {{ \App\Models\WebsiteSetting::primaryColor() }};
        }
    </style>
    @include('components.printer-js')
    @stack('styles')
</head>
