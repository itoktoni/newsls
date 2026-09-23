<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <div class="flex w-full flex-col text-center">
            <h1 class="text-xl font-semibold">{{ __('Reset password') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Please enter your new password below') }}</p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-form :action="route('password.update')" method="POST">
            <div class="bg-base-100 rounded-lg shadow-sm p-4 space-y-4">
                <input type="hidden" name="token" value="{{ request()->route('token') }}">
                <x-input name="email" type="email" :label="__('Email')" :value="request('email')" />
                <x-input name="password" type="password" :label="__('Password')" placeholder="Password" />
                <x-input name="password_confirmation" type="password" :label="__('Confirm password')" placeholder="Confirm password" />

                <x-button type="submit" class="w-full">{{ __('Reset password') }}</x-button>
            </div>
        </x-form>
    </div>
</x-layouts::auth>
