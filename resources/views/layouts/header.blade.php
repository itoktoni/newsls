{{-- Header --}}
<header class="fixed top-0 w-full z-50 bg-surface-container-lowest shadow-sm border-b border-outline-variant rounded-bl-2xl rounded-br-2xl md:rounded-bl-none md:rounded-br-none flex items-center justify-between px-3 md:px-8 h-16">
    <div class="flex min-w-0 items-center gap-2 md:gap-4">
        <button type="button" class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full hover:bg-surface-container transition-colors md:hidden" aria-label="Open navigation menu" @click="drawerOpen = !drawerOpen">
            <span class="material-symbols-outlined text-[22px] text-on-surface-variant">menu</span>
        </button>
        <button type="button" class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-full hover:bg-surface-container transition-colors md:inline-flex" aria-label="Toggle sidebar" @click="sidebarOpen = !sidebarOpen">
            <span class="material-symbols-outlined text-[22px] text-on-surface-variant">menu</span>
        </button>
        <a href="{{ url('/') }}" class="min-w-0 truncate font-headline-md text-headline-md font-bold text-primary" wire:navigate>
            {{ config('website.name', config('app.name', 'Laravel')) }}
        </a>
    </div>

    <div class="flex items-center gap-2">

        {{-- Notification Dropdown --}}
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button class="relative p-2 hover:bg-surface-container rounded-full transition-colors text-on-surface-variant" @click="open = !open" aria-label="Notifications">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                <span class="absolute top-0.5 right-0.5 min-w-4 h-4 px-1 bg-error text-on-error text-[10px] font-bold rounded-full flex items-center justify-center" x-show="unreadCount > 0" x-text="unreadCount > 9 ? '9+' : unreadCount" x-cloak></span>
            </button>

            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="absolute right-0 top-full mt-2 w-[22rem] max-w-[calc(100vw-2rem)] bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-xl overflow-hidden z-50">
                <div class="flex items-center justify-between px-4 py-3 border-b border-outline-variant">
                    <div class="flex items-center gap-2">
                        <span class="font-headline-md text-headline-md text-on-surface">Notifications</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-primary-container text-on-primary-container" x-show="unreadCount > 0" x-text="unreadCount + ' new'" x-cloak></span>
                    </div>
                    <button class="font-label-caps text-label-caps text-primary hover:underline" x-show="unreadCount > 0" @click="markAllRead()">Mark all read</button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <template x-if="notifications.length === 0">
                        <div class="px-4 py-10 text-center">
                            <span class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-surface-container mb-3">
                                <span class="material-symbols-outlined text-3xl text-on-surface-variant/60">notifications_off</span>
                            </span>
                            <p class="font-body-sm font-semibold text-on-surface">No notifications</p>
                            <p class="font-label-caps text-label-caps text-on-surface-variant mt-1">You're all caught up</p>
                        </div>
                    </template>
                    <template x-for="notif in notifications" :key="notif.id">
                        <div class="group flex items-start gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors border-b border-outline-variant/30 cursor-pointer last:border-b-0" :class="{ 'bg-primary-fixed/10': !notif.read }" @click="openNotification(notif)">
                            <span class="w-10 h-10 rounded-full shrink-0 flex items-center justify-center" :style="'color:' + (notif.iconColor || '#176c33') + ';background-color:color-mix(in srgb,' + (notif.iconColor || '#176c33') + ' 14%,white)'">
                                <span class="material-symbols-outlined text-[20px]" x-text="notif.icon || 'notifications'"></span>
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="font-body-sm text-body-sm text-on-surface" :class="{ 'font-semibold': !notif.read }" x-text="notif.title"></p>
                                    <p class="font-label-caps text-label-caps text-on-surface-variant shrink-0 mt-0.5" x-text="notif.time"></p>
                                </div>
                                <p class="font-body-sm text-body-sm text-on-surface-variant mt-0.5 line-clamp-2" x-text="notif.body" x-show="notif.body"></p>
                            </div>
                            <div class="flex flex-col items-center gap-1 shrink-0 mt-1">
                                <div x-show="!notif.read" class="w-2 h-2 bg-primary rounded-full"></div>
                                <button type="button" title="Mark as read" x-show="!notif.read" @click.stop="markRead(notif)" class="hidden group-hover:flex p-1 rounded-full hover:bg-surface-container text-on-surface-variant hover:text-primary transition-colors">
                                    <span class="material-symbols-outlined text-[16px]">check</span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Profile Dropdown --}}
        <div class="relative" x-data="{ open: false }" @click.away="open = false">
            <button class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center overflow-hidden border border-outline-variant hover:ring-2 hover:ring-primary/20 transition-all" @click="open = !open">
                <span class="material-symbols-outlined text-[18px] text-on-secondary-container">person</span>
            </button>

            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="absolute right-0 top-full mt-2 w-64 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-outline-variant">
                    <p class="font-body-sm font-semibold text-on-surface">{{ auth()->user()->name ?? 'Warehouse Admin' }}</p>
                    <p class="font-label-caps text-label-caps text-on-surface-variant">{{ auth()->user()->email ?? 'admin@wms.com' }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.edit') }}" wire:navigate class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-surface-container-low transition-colors text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined text-xl">person</span>
                        <span class="font-body-sm text-body-sm">My Profile</span>
                    </a>
                    <a href="#" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-surface-container-low transition-colors text-on-surface-variant hover:text-on-surface">
                        <span class="material-symbols-outlined text-xl">help</span>
                        <span class="font-body-sm text-body-sm">Help & Support</span>
                    </a>
                </div>
                <div class="border-t border-outline-variant py-1">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-error-container/30 transition-colors text-error">
                            <span class="material-symbols-outlined text-xl">logout</span>
                            <span class="font-body-sm text-body-sm font-semibold">Sign Out</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
