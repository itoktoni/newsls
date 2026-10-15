<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

                <x-form-select col="6" class="search" name="system_menu_type" :options="$type" />
                <x-form-input col="6" name="system_menu_name" />
                <x-form-select col="6" class="search" name="system_menu_controller" :options="$file" />
                <x-form-select col="6" class="search" name="system_menu_action" :options="$action" />
                <x-form-input col="6" name="system_menu_url" />
                <x-form-input col="6" name="system_menu_sort" />

				@if(!empty($model) && $model->field_type == MenuType::Group)
                <x-form-select col="12" class="search" multiple name="link[]" :options="$link" :default="$selected" />
				@endif

			@endbind

        </x-form>
    </x-card>
    <x-script-form />
</x-layout>
