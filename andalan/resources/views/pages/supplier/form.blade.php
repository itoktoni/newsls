<x-layout>
    <x-card>
        <x-form :model="$model">
            <x-action form="form" />

            @bind($model)

            <x-form-input col="6" name="supplier_nama" />
            <x-form-input col="6" name="supplier_kontak" />
            <x-form-input col="6" name="supplier_phone" />
            <x-form-input col="6" name="supplier_email" />
            <x-form-textarea col="6" name="supplier_alamat" />

            @endbind

        </x-form>
    </x-card>
</x-layout>
