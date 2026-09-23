<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <div class="flex w-full flex-col text-center">
            <h1 class="text-xl font-semibold">{{ __('Create an account') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Enter your details below to create your account') }}</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-form :action="route('register.store')" method="POST">
            <div class="bg-base-100 rounded-lg shadow-sm p-4 space-y-4">
                <x-input name="name" type="text" :label="__('Name')" placeholder="Full name" />
                <x-input name="email" type="email" :label="__('Email address')" placeholder="email@example.com" />
                <x-input name="password" type="password" :label="__('Password')" placeholder="Password" />
                <x-input name="password_confirmation" type="password" :label="__('Confirm password')" placeholder="Confirm password" />

                <x-button type="submit" class="w-full">{{ __('Create account') }}</x-button>
            </div>
        </x-form>

        <div class="text-center text-sm text-base-content/60">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="link link-primary">{{ __('Log in') }}</a>
        </div>
    </div>
</x-layouts::auth>
