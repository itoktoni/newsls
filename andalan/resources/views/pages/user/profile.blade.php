<x-layout>
    <x-card label="Profile">
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)
                <x-form-input col="6" name="name" />
                <x-form-input col="6" name="username" />
                <x-form-input col="6" name="password" type="password"/>
                <x-form-input col="3" name="phone" />
                <x-form-input col="3" name="email" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
