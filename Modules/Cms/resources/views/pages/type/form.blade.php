<?php /** @var Modules\Cms\Models\Type $model */ ?>

<x-layouts::app>
    <x-breadcrumb :items="[['url' => moduleRoute('getTable'), 'label' => 'Types'], ['url' => '', 'label' => isset($model) && $model->exists ? 'Update' : 'Create']]" />

    <x-form :model="$model">
        <x-card label="Types">
            @bind($model ?? null)
                <x-input col="6" name="name" />
                <x-input col="6" name="slug" />
                <x-select col="6" name="type" :options="$typeOptions" />
                <x-textarea col="6" name="description" />
                <x-select col="6" name="is_active" :options="['1' => 'Active', '0' => 'Inactive']"/>
                <x-input col="6" name="menu_position" type="number" />
                <x-input col="6" name="menu_icon" />
            @endbind
        </x-card>

        {{-- Supports Configuration --}}
        <x-card label="Supports" class="mt-5">
            <div class="flex flex-col gap-2">
                @foreach($supportsOptions as $value => $label)
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="supports[]" value="{{ $value }}" 
                            {{ (isset($model) && in_array($value, $model->supports ?? [])) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </x-card>

        <x-action :model="$model" :action="['save']"/>
    </x-form>
</x-layouts::app>

