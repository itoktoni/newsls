<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)
                <x-form-input col="3" name="system_group_code" />
                <x-form-input col="3" name="system_group_sort" />
                <x-form-input col="6" name="system_group_name" />
                <x-form-input col="6" label="Icon - https://icons.getbootstrap.com" name="system_group_icon" />
                <x-form-input col="6" name="system_group_url" />
                <x-form-select col="12" class="tag" multiple name="menu[]" :default="$selected ?? []" :options="$menu" />
            @endbind

        </x-form>
    </x-card>
</x-layout>
