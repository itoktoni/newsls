<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

                <x-form-select col="6" class="search" name="system_link_type" :options="$type" />
                <x-form-input col="6" name="system_link_name" />
                <x-form-input col="6" name="system_link_url" />
                <x-form-input col="6" name="system_link_sort" />
                <x-form-select col="6" class="search" name="system_link_controller" :options="$file" />
                <x-form-select col="6" class="search" name="system_link_action" :options="$action" />

                @if(!empty($model) && $model->field_type == MenuType::Group)
                    <x-form-select col="12" class="search" multiple name="link[]" :options="$link"
                        :default="$selected" />
                @endif

            @endbind

        </x-form>
    </x-card>
</x-layout>
