<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <div class="flex w-full flex-col text-center">
            <h1 class="text-xl font-semibold">{{ __('Forgot password') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Enter your email to receive a password reset link') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-form :action="route('password.email')" method="POST">
            <div class="bg-base-100 rounded-lg shadow-sm p-4 space-y-4">
                <x-input name="email" type="email" :label="__('Email address')" placeholder="email@example.com" />

                <x-button type="submit" class="w-full">{{ __('Email password reset link') }}</x-button>
            </div>
        </x-form>

        <div class="text-center text-sm text-base-content/60">
            <span>{{ __('Or, return to') }}</span>
            <a href="{{ route('login') }}" class="link link-primary">{{ __('log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
