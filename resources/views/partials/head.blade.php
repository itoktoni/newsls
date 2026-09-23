<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="theme-color" content="#000000" />

@php
    $wsName = ($websiteSettings ?? [])['name'] ?? config('app.name', 'Laravel');
    $faviconUrl = \App\Models\WebsiteSetting::fileUrl(($websiteSettings ?? [])['favicon'] ?? null) ?? '/favicon.ico';
@endphp

<title>{{ filled($title ?? null) ? $title.' - '.$wsName : $wsName }}</title>

<link rel="icon" href="{{ $faviconUrl }}" sizes="any">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/manifest.json">

@vite(['resources/css/app.css', 'resources/js/app.js'])
