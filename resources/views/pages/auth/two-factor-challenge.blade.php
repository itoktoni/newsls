<x-layouts::auth :title="__('Two-factor authentication')">
    <div class="flex flex-col gap-6">
        <div
            x-data="{
                showRecoveryInput: @js($errors->has('recovery_code')),
                code: '',
                recovery_code: '',
                init() {
                    if (! this.showRecoveryInput) {
                        this.$nextTick(() => this.$refs.otp?.querySelector('input')?.focus());
                    }
                },
                toggleInput() {
                    this.showRecoveryInput = !this.showRecoveryInput;
                    this.code = '';
                    this.recovery_code = '';
                    this.$nextTick(() => {
                        if (this.showRecoveryInput) {
                            this.$refs.recovery_code?.focus();
                        } else {
                            this.$refs.otp?.querySelector('input')?.focus();
                        }
                    });
                },
            }"
            class="w-full"
        >
            <div x-show="!showRecoveryInput">
                <div class="flex w-full flex-col text-center">
                    <h1 class="text-xl font-semibold">{{ __('Authentication code') }}</h1>
                    <p class="text-sm text-base-content/60">{{ __('Enter the authentication code provided by your authenticator application.') }}</p>
                </div>
            </div>

            <div x-show="showRecoveryInput">
                <div class="flex w-full flex-col text-center">
                    <h1 class="text-xl font-semibold">{{ __('Recovery code') }}</h1>
                    <p class="text-sm text-base-content/60">{{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('two-factor.login.store') }}" class="mt-6">
                @csrf

                <div class="bg-base-100 rounded-lg shadow-sm p-4 space-y-4">
                    <div x-show="!showRecoveryInput">
                        <div class="flex justify-center my-5" x-ref="otp">
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    name="code"
                                    x-model="code"
                                    maxlength="6"
                                    class="input input-bordered w-12 text-center font-mono text-lg tracking-widest"
                                    placeholder="-"
                                    autocomplete="one-time-code"
                                />
                            </div>
                        </div>
                    </div>

                    <div x-show="showRecoveryInput">
                        <div class="my-5">
                            <input
                                type="text"
                                name="recovery_code"
                                x-ref="recovery_code"
                                x-bind:required="showRecoveryInput"
                                x-model="recovery_code"
                                class="input input-bordered w-full"
                                placeholder="Recovery code"
                                autocomplete="one-time-code"
                            />
                        </div>

                        @error('recovery_code')
                            <p class="text-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('Continue') }}
                    </button>
                </div>

                <div class="mt-4 text-center">
                    <span class="text-sm text-base-content/50">{{ __('or you can') }}</span>
                    <button type="button" @click="toggleInput()" class="btn btn-ghost btn-sm normal-case underline text-sm ml-1">
                        <span x-show="!showRecoveryInput">{{ __('login using a recovery code') }}</span>
                        <span x-show="showRecoveryInput">{{ __('login using an authentication code') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::auth>
