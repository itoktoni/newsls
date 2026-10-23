<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)
                <x-form-input col="6" name="system_role_name" />
                <x-form-input col="6" name="system_role_description" />
                <x-form-select col="6" name="system_role_level" :options="$level" />
                <x-form-select col="6" class="tag" multiple name="group[]" :default="$selected ?? []" :options="$group" />
            @endbind

        </x-form>
    </x-card>
    <x-script-form />
</x-layout>
