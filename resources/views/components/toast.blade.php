@props([])

@php
$toasts  = session('toasts', []);
$classes = [
    'success' => 'bg-green-50 border-green-200 text-green-800',
    'danger'  => 'bg-red-50 border-red-200 text-red-800',
    'error'   => 'bg-red-50 border-red-200 text-red-800',
    'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
    'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
];
$icons = [
    'success' => 'check_circle',
    'danger'  => 'error',
    'error'   => 'error',
    'warning' => 'warning',
    'info'    => 'info',
];
@endphp

{{-- Toast container — session-based toasts (form success/error) --}}
<div
    id="toast-container"
    class="fixed bottom-4 right-4 z-50 space-y-2 w-80"
>
    @foreach ($toasts as $toast)
        @php
            $cls  = $classes[$toast['type']] ?? $classes['info'];
            $icon = $icons[$toast['type']] ?? $icons['info'];
            $dur  = $toast['duration'] ?? 5000;
        @endphp
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, {{ $dur }})"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="flex items-start gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium border {{ $cls }}"
        >
            <span class="material-symbols-outlined text-base mt-0.5">{{ $icon }}</span>
            <div class="flex-1">
                @if (! empty($toast['heading']))
                    <div class="font-semibold">{{ $toast['heading'] }}</div>
                @endif
                <div>{{ $toast['message'] }}</div>
            </div>
            <button type="button" @click="show = false" class="ml-2 shrink-0 hover:opacity-70">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
    @endforeach
</div>