<x-layouts::auth :title="__('Email verification')">
    <div class="flex flex-col gap-6">
        <div class="flex w-full flex-col text-center">
            <h1 class="text-xl font-semibold">{{ __('Verify your email') }}</h1>
            <p class="text-sm text-base-content/60">{{ __('Please verify your email address by clicking on the link we just emailed to you.') }}</p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="alert alert-success">
                <span class="icon-[tabler--check] size-5"></span>
                <span>{{ __('A new verification link has been sent to the email address you provided during registration.') }}</span>
            </div>
        @endif

        <div class="bg-base-100 rounded-lg shadow-sm p-4 space-y-4">
            <x-form :action="route('verification.send')" method="POST">
                <x-button type="submit" class="w-full">{{ __('Resend verification email') }}</x-button>
            </x-form>

            <x-form :action="route('logout')" method="POST">
                <x-button type="submit" variant="ghost" class="w-full">{{ __('Log out') }}</x-button>
            </x-form>
        </div>
    </div>
</x-layouts::auth>
