@auth
<div class="p-2 border-t border-outline-variant">
    <button onclick="document.getElementById('profMenu').classList.toggle('hidden')"
        class="flex items-center gap-2 w-full px-2 py-1.5 rounded-lg hover:bg-surface-container cursor-pointer transition-colors">
        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="material-symbols-outlined text-primary text-sm">person</span>
        </div>
        <div class="flex-1 min-w-0 text-left">
            <p class="font-body-sm text-body-sm font-semibold text-on-surface truncate">{{ ucfirst(auth()->user()->role ?? 'user') }}</p>
            <p class="font-label-caps text-label-caps text-on-surface-variant truncate">{{ auth()->user()->name }}</p>
        </div>
        <span class="material-symbols-outlined text-sm text-on-surface-variant">more_vert</span>
    </button>
    <div id="profMenu" onmouseleave="document.getElementById('profMenu').classList.add('hidden')" class="hidden absolute bottom-full left-2 right-2 mb-1 bg-surface-container-lowest border border-outline-variant rounded-xl shadow-lg z-50 py-1">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-3 py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-lg">person</span>Profile
        </a>
        <a href="#" class="flex items-center gap-2 px-3 py-2 font-body-sm text-body-sm text-on-surface-variant hover:bg-surface-container transition-colors">
            <span class="material-symbols-outlined text-lg">settings</span>Settings
        </a>
        <div class="border-t border-outline-variant my-1"></div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 font-body-sm text-body-sm text-error hover:bg-error-container/30 transition-colors">
                <span class="material-symbols-outlined text-lg">logout</span>Logout
            </button>
        </form>
    </div>
</div>
@endauth
