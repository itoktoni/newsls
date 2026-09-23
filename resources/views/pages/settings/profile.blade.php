<x-layouts::app title="My Profile">
    <x-breadcrumb :items="[['url' => route('dashboard'), 'label' => 'Dashboard'], ['url' => '', 'label' => 'My Profile']]" />


    {{-- Tabs --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden" x-data="{ tab: 'info' }">
        <div class="flex overflow-x-auto border-b border-outline-variant bg-surface-container">
            <button @click="tab = 'info'" :class=" tab === 'info' ? 'border-primary text-primary bg-surface-container-lowest' : 'border-transparent text-on-surface-variant hover:text-on-surface'" class="flex items-center gap-2 px-4 md:px-5 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-lg">person</span> Information
            </button>
            <button @click="tab = 'sessions'" :class="tab === 'sessions' ? 'border-primary text-primary bg-surface-container-lowest' : 'border-transparent text-on-surface-variant hover:text-on-surface'" class="flex items-center gap-2 px-4 md:px-5 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-lg">devices</span> Active Sessions
            </button>
            <button @click="tab = 'danger'" :class="tab === 'danger' ? 'border-error text-error bg-surface-container-lowest' : 'border-transparent text-on-surface-variant hover:text-on-surface'" class="flex items-center gap-2 px-4 md:px-5 py-4 text-sm font-semibold border-b-2 transition-colors whitespace-nowrap">
                <span class="material-symbols-outlined text-lg">warning</span> Danger Zone
            </button>
        </div>

        {{-- Tab: Information --}}
        <div x-show="tab === 'info'" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Profile Information --}}
                {{-- Avatar Upload --}}
                <div class="bg-surface-container border border-outline-variant rounded-xl p-5 flex flex-col items-center">
                    <h3 class="font-headline-md text-headline-md text-on-surface pb-3 mb-4 border-b border-outline-variant w-full flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-xl">photo_camera</span>
                        Avatar
                    </h3>
                    <div class="w-24 h-24 rounded-full bg-surface-container-high border-2 border-outline-variant overflow-hidden mb-4">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&size=96&background=3755c3&color=fff' }}"
                             alt="{{ $user->name }}" class="w-full h-full object-cover">
                    </div>
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="text-center">
                        @csrf
                        <input type="hidden" name="name" value="{{ old('name', $user->name) }}">
                        <input type="hidden" name="email" value="{{ old('email', $user->email) }}">
                        <label class="cursor-pointer bg-primary text-on-primary px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-90 transition-opacity inline-flex items-center gap-2">
                            <span class="material-symbols-outlined text-lg">upload</span>
                            Upload Photo
                            <input type="file" name="avatar" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" onchange="this.form.submit()">
                        </label>
                        <p class="text-xs text-on-surface-variant mt-2">Max 1MB. JPG, PNG, GIF, WebP.</p>
                        @error('avatar')<p class="text-xs text-error mt-1">{{ $message }}</p>@enderror
                    </form>
                </div>

                <x-form :action="route('profile.update')" method="POST">
                    <div class="bg-surface-container border border-outline-variant rounded-xl p-5 h-full">
                        <h3 class="font-headline-md text-headline-md text-on-surface pb-3 mb-4 border-b border-outline-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-xl">person</span>
                            Profile Information
                        </h3>
                        @bind($user ?? null)
                            <div class="space-y-4">
                                <x-input col="12" name="name" label="Full Name" />
                                <x-input col="12" name="email" type="email" label="Email Address" />
                            </div>
                        @endbind
                        <div class="flex justify-end mt-4 pt-3 border-t border-outline-variant">
                            <x-button type="submit" icon="save">Save Changes</x-button>
                        </div>
                    </div>
                </x-form>

                {{-- Change Password --}}
                <form action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    <div class="bg-surface-container border border-outline-variant rounded-xl p-5 h-full">
                        <h3 class="font-headline-md text-headline-md text-on-surface pb-3 mb-4 border-b border-outline-variant flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-xl">lock</span>
                            Change Password
                        </h3>
                        <div class="space-y-4">
                            <x-input col="12" name="current_password" type="password" label="Current Password" />
                            <x-input col="12" name="password" type="password" label="New Password" />
                            <x-input col="12" name="password_confirmation" type="password" label="Confirm Password" />
                        </div>
                        <div class="flex justify-end mt-4 pt-3 border-t border-outline-variant">
                            <x-button type="submit" icon="save">Update Password</x-button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Email Verification --}}
            @if ($user && !$user->hasVerifiedEmail())
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-6">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-amber-600 text-xl">warning</span>
                    <div class="flex-1">
                        <p class="font-body-sm text-body-sm text-amber-800">Your email address is unverified.</p>
                        <form action="{{ route('verification.send') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="font-body-sm text-body-sm text-primary hover:underline font-semibold">
                                Click here to re-send the verification email.
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Tab: Active Sessions --}}
        <div x-show="tab === 'sessions'" class="p-6">
            {{-- Current Session --}}
            <div class="flex items-center gap-4 p-4 bg-primary-fixed/20 border border-primary/20 rounded-xl mb-3">
                <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">computer</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-body-sm text-body-sm font-semibold text-on-surface">This Device</p>
                    <p class="font-label-caps text-label-caps text-on-surface-variant truncate">
                        {{ request()->userAgent() }} &middot; {{ request()->ip() }}
                    </p>
                </div>
                <span class="font-label-caps text-label-caps bg-primary/10 text-primary px-2.5 py-1 rounded-full shrink-0">Current</span>
            </div>

            {{-- Other Sessions --}}
            @if($otherSessions->count() > 0)
                @foreach($otherSessions as $session)
                <div class="flex items-center gap-4 p-4 bg-surface-container border border-outline-variant rounded-xl mb-2">
                    <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-surface-variant">computer</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-body-sm text-body-sm text-on-surface">Other Device</p>
                        <p class="font-label-caps text-label-caps text-on-surface-variant truncate">
                            {{ $session->user_agent ?? 'Unknown' }} &middot; {{ $session->ip_address ?? 'Unknown' }}
                        </p>
                    </div>
                </div>
                @endforeach

                <form action="{{ route('profile.sessions.delete') }}" method="POST" class="mt-6 pt-4 border-t border-outline-variant">
                    @csrf
                    <div class="flex items-end gap-4">
                        <div class="flex-1">
                            <x-input name="password" type="password" label="Confirm Password to Logout Other Sessions" placeholder="Enter your password" />
                        </div>
                        <div class="pb-0.5">
                            <x-button type="submit" variant="error" icon="logout">Logout Other Sessions</x-button>
                        </div>
                    </div>
                </form>
            @else
                <div class="text-center py-8">
                    <span class="material-symbols-outlined text-on-surface-variant/30 text-5xl mb-2 block">devices</span>
                    <p class="font-body-sm text-on-surface-variant">No other active sessions.</p>
                </div>
            @endif
        </div>

        {{-- Tab: Danger Zone --}}
        <div x-show="tab === 'danger'" class="p-6">
            <div class="bg-error-container/20 border border-error/20 rounded-xl p-5">
                <h3 class="font-headline-md text-headline-md text-error pb-3 mb-4 border-b border-error/20 flex items-center gap-2">
                    <span class="material-symbols-outlined text-error text-xl">warning</span>
                    Delete Account
                </h3>
                <p class="font-body-sm text-on-surface-variant mb-4">Once you delete your account, all of its resources and data will be permanently deleted. Please enter your password to confirm.</p>
                <form action="{{ route('profile.destroy') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-end gap-4">
                        <div class="flex-1 max-w-sm">
                            <x-input name="password" type="password" label="Confirm Password" placeholder="Enter your password" />
                        </div>
                        <div class="pb-0.5">
                            <x-button type="submit" variant="error" icon="delete">Delete Account</x-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts::app>
