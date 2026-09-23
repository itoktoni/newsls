<div class="container">

    <div class="row">
        <x-form-select col="{{ $hide ? 6 : 4 }}" wire:model.live="id_rs" label="Rumah sakit" name="view_rs_id" value="{{ $id_rs }}" :options="$data_rs" />
        @if($hide != 'ruangan')
        <x-form-select col="{{ $hide ? 6 : 4 }}" wire:model="id_ruangan" label="Ruangan" name="view_ruangan_id" value="{{ $id_ruangan }}" :options="$data_ruangan" />
        @endif
        @if($hide != 'jenis')
        <x-form-select col="{{ $hide ? 6 : 4 }}" wire:model="id_jenis" label="Jenis" name="view_linen_id" value="{{ $id_jenis }}" :options="$data_jenis" />
        @endif
    </div>

</div>