<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="shadcn">
<head>
    @php
        $websiteSettings = \App\Models\WebsiteSetting::merged();
    @endphp
    @include('partials.head', ['websiteSettings' => $websiteSettings])
</head>
<body class="min-h-screen bg-base-200 antialiased">
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6">
        <div class="flex w-full max-w-md flex-col">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium mb-4">
                @php
                    $logoUrl = \App\Models\WebsiteSetting::fileUrl($websiteSettings['logo'] ?? null);
                @endphp
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $websiteSettings['name'] ?? config('app.name', 'Laravel') }}" class="h-12 w-auto object-contain">
                @else
                    <span class="flex h-10 w-10 items-center justify-center rounded-md bg-primary">
                        <x-app-logo-icon class="size-6 fill-current text-primary-content" />
                    </span>
                @endif
                <span class="sr-only">{{ $websiteSettings['name'] ?? config('app.name', 'Laravel') }}</span>
            </a>
            <div class="flex flex-col gap-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
