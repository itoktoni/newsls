@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center">
    <h2 class="font-headline-lg text-headline-lg text-on-surface">{{ $title }}</h2>
    <p class="font-body-sm text-on-surface-variant mt-1">{{ $description }}</p>
</div>
