<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

                <x-form-select col="6" class="search" name="system_permision_controller" :options="$file" />
                <x-form-select col="6" name="system_permision_role" :options="$role" />
                <x-form-select col="6" class="search" name="system_permision_user" :options="$user" />
                <x-form-select col="6" name="system_permision_level" :options="$level" />
                <x-form-select col="6" name="system_permision_code" :options="$action" />

            @endbind

        </x-form>
    </x-card>
    <x-script-form />
</x-layout>
