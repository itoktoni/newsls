<div class="container">
    <div class="row">
        <x-form-select col="6" wire:change="change" wire:model="role_id" name="role" :options="$this->data_role" />
        @if (!$hide)
            <x-form-select col="6" wire:model="rs_id" name="id_rs" :options="$this->data_rs" />
        @endif
    </div>
</div>
