<x-layouts::app title="Settings - .env Editor">
    <x-breadcrumb :items="[['url' => route('dashboard'), 'label' => 'Dashboard'], ['url' => '', 'label' => 'Settings']]" />

    <form action="{{ route('settings.env.save') }}" method="POST">
        @csrf
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4">
            <h3 class="font-headline-md text-headline-md text-on-surface pb-4 mb-4 border-b border-outline-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-xl">settings</span>
                Environment Configuration (.env)
            </h3>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-amber-600 text-xl mt-0.5">warning</span>
                    <div>
                        <p class="font-body-sm text-body-sm font-semibold text-amber-800">Caution</p>
                        <p class="font-body-sm text-body-sm text-amber-700 mt-1">Editing the .env file directly can break your application. Only modify values if you know what you're doing. Some changes require a server restart to take effect.</p>
                    </div>
                </div>
            </div>

            <textarea
                name="env_content"
                rows="30"
                class="w-full px-4 py-3 bg-white border border-outline-variant rounded-lg font-mono text-sm leading-relaxed focus:border-primary-container focus:ring-1 focus:ring-primary-container outline-none transition-all resize-y"
                spellcheck="false"
            >{{ $envContent }}</textarea>

            <div class="flex items-center justify-between mt-4 pt-4 border-t border-outline-variant">
                <p class="font-label-caps text-label-caps text-on-surface-variant">
                    File: <span class="font-mono text-on-surface">{{ base_path('.env') }}</span>
                </p>
            </div>
        </div>

        <style>
            @media (min-width: 768px) { .action-bar { bottom: 0 !important; } }
        </style>
        <div class="action-bar fixed left-0 right-0 lg:left-72 bg-surface-container-lowest border-t border-outline-variant shadow-[0_-4px_12px_rgba(0,0,0,0.08)] px-4 md:px-6 py-3 z-[45]" style="bottom: 4rem">
            <div class="flex items-center justify-end max-w-full mx-auto gap-3">
                <x-button type="submit" icon="save">Save</x-button>
            </div>
        </div>
    </form>

    {{-- Quick Info --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6 pb-16 md:pb-16">
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-primary text-xl">info</span>
                <h4 class="font-body-sm text-body-sm font-semibold text-on-surface">APP_KEY</h4>
            </div>
            <p class="font-label-caps text-label-caps text-on-surface-variant">Used for encryption. Never share this key.</p>
            <p class="font-mono text-xs text-on-surface mt-2 truncate">{{ config('app.key') ? 'Set' : 'Not Set' }}</p>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-secondary text-xl">storage</span>
                <h4 class="font-body-sm text-body-sm font-semibold text-on-surface">Database</h4>
            </div>
            <p class="font-label-caps text-label-caps text-on-surface-variant">Current database connection.</p>
            <p class="font-mono text-xs text-on-surface mt-2">{{ config('database.default') }} ({{ config('database.connections.' . config('database.default') . '.host') }})</p>
        </div>
        <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-4">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-outlined text-on-surface-variant text-xl">bug_report</span>
                <h4 class="font-body-sm text-body-sm font-semibold text-on-surface">Debug Mode</h4>
            </div>
            <p class="font-label-caps text-label-caps text-on-surface-variant">Current debug status.</p>
            <p class="font-mono text-xs mt-2">
                @if(config('app.debug'))
                    <span class="text-error font-semibold">Enabled</span>
                @else
                    <span class="text-green-600 font-semibold">Disabled</span>
                @endif
            </p>
        </div>
    </div>
</x-layouts::app>
