<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <nav class="menu bg-base-200 rounded-box w-full">
            <li>
                <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                    {{ __('Profile') }}
                </a>
            </li>
            <li>
                <a href="{{ route('security.edit') }}" class="{{ request()->routeIs('security.edit') ? 'active' : '' }}">
                    {{ __('Security') }}
                </a>
            </li>
            <li>
                <a href="{{ route('appearance.edit') }}" class="{{ request()->routeIs('appearance.edit') ? 'active' : '' }}">
                    {{ __('Appearance') }}
                </a>
            </li>
        </nav>
    </div>

    <div class="flex-1 self-stretch max-md:pt-6">
        <h2 class="text-2xl font-bold mb-1">{{ $heading ?? '' }}</h2>
        <p class="text-sm text-base-content/60 mb-5">{{ $subheading ?? '' }}</p>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
