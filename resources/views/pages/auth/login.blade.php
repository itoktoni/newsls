<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <div class="flex w-full flex-col text-center">
            <h1 class="text-xl font-semibold">{{ __('Log in to') }} {{ config('app.name') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Enter your email and password below to log in') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-form :action="route('login.store')" method="POST">
            <div class="bg-base-100 rounded-lg shadow-sm p-4 space-y-4">
                <x-input name="email" type="email" :label="__('Email address')" placeholder="email@example.com" />
                <x-input name="password" type="password" :label="__('Password')" placeholder="Password" />

                <div class="flex items-center justify-between">
                    <label class="cursor-pointer label justify-start gap-2">
                        <input type="checkbox" name="remember" class="checkbox checkbox-primary checkbox-sm" {{ old('remember') ? 'checked' : '' }} />
                        <span class="label-text text-sm">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <x-button type="submit" class="w-full">{{ __('Log in') }}</x-button>
            </div>
        </x-form>

        @if (Route::has('password.request'))
            <div class="text-center text-sm text-base-content/60">
                <span>{{ __("Don't have an account?") }}</span>
                <a href="{{ route('register') }}" class="link link-primary">{{ __('Sign up') }}</a>
            </div>
        @endif
    </div>
</x-layouts::auth>
