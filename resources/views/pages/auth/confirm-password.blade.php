<x-layouts::auth :title="__('Confirm password')">
    <div class="flex flex-col gap-6">
        <div class="flex w-full flex-col text-center">
            <h1 class="text-xl font-semibold">{{ __('Confirm password') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        <x-form :action="route('password.confirm.store')" method="POST">
            <div class="bg-base-100 rounded-lg shadow-sm p-4 space-y-4">
                <x-input name="password" type="password" :label="__('Password')" placeholder="Password" />

                <x-button type="submit" class="w-full">{{ __('Confirm') }}</x-button>
            </div>
        </x-form>
    </div>
</x-layouts::auth>
